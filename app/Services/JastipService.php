<?php

namespace App\Services;

use App\Enums\JastipOrderStatus;
use App\Models\{JastipOrder, JastipOrderItem, JastipStop, Mitra};
use Illuminate\Support\Facades\DB;

class JastipService
{
    public const KOMISI_RATE = 0.05;       // 5% dari ongkos jasa
    public const TARIF_PER_KM_DEFAULT = 3000;
    public const BIAYA_PER_STOP = 5000;
    public const MAX_DEVIASI_HARGA = 0.30; // 30% deviasi max actual vs estimasi

    // ── Pelanggan: buat order Jastip ──────────────────────────────────────────
    public function buatOrder(array $data, int $pelangganId): JastipOrder
    {
        return DB::transaction(function () use ($data, $pelangganId) {
            $stops          = $data['stops'];           // array of {nama_lokasi, alamat_lokasi, lat, lng, items: [...]}
            $deliveryAddr   = $data['delivery_address'];
            $deliveryLat    = $data['delivery_lat'];
            $deliveryLng    = $data['delivery_lng'];
            $mitraId        = $data['mitra_id'] ?? null;

            // Hitung total jarak (sederhana: titik ke titik via Haversine)
            [$totalJarak, $stopJarak] = $this->hitungJarak($stops, $deliveryLat, $deliveryLng);

            $tarifPerKm     = self::TARIF_PER_KM_DEFAULT;
            $biayaStop      = self::BIAYA_PER_STOP * count($stops);
            $ongkosJasa     = round(($totalJarak * $tarifPerKm) + $biayaStop, 0);
            $komisi         = round($ongkosJasa * self::KOMISI_RATE, 0);
            $pendapatanMitra= $ongkosJasa - $komisi;

            // Estimasi total barang dari item perkiraan
            $estimasiBarang = collect($stops)->flatMap(fn($s) => $s['items'])
                ->sum(fn($it) => (float) $it['harga_perkiraan']);

            // Cek COD eligible: saldo mitra >= komisi
            $codEligible = false;
            $saldoSnapshot = 0;
            if ($mitraId) {
                $mitra = Mitra::where('id_mitra', $mitraId)->first();
                if ($mitra) {
                    $saldoSnapshot = (float) $mitra->saldo;
                    $codEligible = $saldoSnapshot >= $komisi;
                }
            }

            $order = JastipOrder::create([
                'order_code'           => JastipOrder::generateCode(),
                'pelanggan_id'         => $pelangganId,
                'mitra_id'             => $mitraId,
                'delivery_address'     => $deliveryAddr,
                'delivery_lat'         => $deliveryLat,
                'delivery_lng'         => $deliveryLng,
                'total_jarak_km'       => $totalJarak,
                'total_stops'          => count($stops),
                'tarif_per_km'         => $tarifPerKm,
                'biaya_stop'           => self::BIAYA_PER_STOP,
                'ongkos_jasa'          => $ongkosJasa,
                'komisi_zasha'         => $komisi,
                'pendapatan_mitra'     => $pendapatanMitra,
                'estimasi_total_barang'=> $estimasiBarang,
                'actual_total_barang'  => 0,
                'saldo_mitra_snapshot' => $saldoSnapshot,
                'cod_eligible'         => $codEligible,
                'status'               => JastipOrderStatus::MenungguMitra,
                'mitra_notified_at'    => now(),
                'auto_reject_at'       => now()->addMinutes(15),
            ]);

            // Buat stops dan items
            foreach ($stops as $i => $stopData) {
                $stop = JastipStop::create([
                    'jastip_order_id'    => $order->id,
                    'urutan'             => $i + 1,
                    'nama_lokasi'        => $stopData['nama_lokasi'],
                    'alamat_lokasi'      => $stopData['alamat_lokasi'],
                    'lat'                => $stopData['lat'],
                    'lng'                => $stopData['lng'],
                    'jarak_dari_prev_km' => $stopJarak[$i] ?? 0,
                ]);

                foreach ($stopData['items'] as $j => $itemData) {
                    JastipOrderItem::create([
                        'jastip_order_id' => $order->id,
                        'jastip_stop_id'  => $stop->id,
                        'urutan'          => $j + 1,
                        'nama_barang'     => $itemData['nama_barang'],
                        'harga_perkiraan' => $itemData['harga_perkiraan'],
                        'catatan'         => $itemData['catatan'] ?? null,
                    ]);
                }
            }

            return $order->fresh(['stops', 'items']);
        });
    }

    // ── Mitra: terima order ───────────────────────────────────────────────────
    public function terimaOrder(JastipOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);

        // Re-validasi COD eligible saat terima
        $mitra = Mitra::where('id_mitra', $mitraIdMitra)->lockForUpdate()->firstOrFail();
        if ($mitra->saldo < $order->komisi_zasha) {
            throw new \RuntimeException('Saldo Anda tidak cukup untuk komisi. Minimal: Rp ' . number_format($order->komisi_zasha));
        }

        $order->transitionTo(JastipOrderStatus::MenujuPickup);
        $order->update([
            'mitra_responded_at' => now(),
            'pickup_started_at'  => now(),
        ]);
    }

    // ── Mitra: tolak order ────────────────────────────────────────────────────
    public function tolakOrder(JastipOrder $order, int $mitraIdMitra, string $alasan): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(JastipOrderStatus::Ditolak);
        $order->update([
            'alasan_penolakan'   => $alasan,
            'mitra_responded_at' => now(),
        ]);
    }

    // ── Mitra: tiba di stop ───────────────────────────────────────────────────
    public function tibaDiStop(JastipStop $stop, int $mitraIdMitra): void
    {
        $this->assertMitra($stop->order, $mitraIdMitra);
        $stop->update(['tiba_at' => now()]);

        // Jika belum status belanja, transisi
        if ($stop->order->status === JastipOrderStatus::MenujuPickup) {
            $stop->order->transitionTo(JastipOrderStatus::Belanja);
        }
    }

    // ── Mitra: checklist item dengan harga asli ──────────────────────────────
    public function checklistItem(JastipOrderItem $item, int $mitraIdMitra, float $hargaAsli): void
    {
        $order = $item->order;
        $this->assertMitra($order, $mitraIdMitra);

        // Validasi deviasi harga
        $deviasi = abs($hargaAsli - $item->harga_perkiraan) / max($item->harga_perkiraan, 1);
        if ($deviasi > self::MAX_DEVIASI_HARGA) {
            throw new \RuntimeException(sprintf(
                'Harga %s deviasi %d%% (max 30%%). Pelanggan perlu konfirmasi terlebih dahulu.',
                $item->nama_barang, $deviasi * 100
            ));
        }

        $item->update([
            'harga_asli' => $hargaAsli,
            'is_checked' => true,
            'checked_at' => now(),
        ]);

        // Update actual_total_barang
        $order->update([
            'actual_total_barang' => $order->items()->where('is_checked', true)->sum('harga_asli'),
        ]);
    }

    // ── Mitra: selesai belanja, menuju pengantaran ───────────────────────────
    public function mulaiAntar(JastipOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);

        if (! $order->allItemsChecked()) {
            throw new \RuntimeException('Masih ada item yang belum dichecklist.');
        }

        $order->transitionTo(JastipOrderStatus::MenujuPengantaran);
        $order->update(['delivery_started_at' => now()]);
    }

    // ── Mitra: barang sudah diantar ──────────────────────────────────────────
    public function diantar(JastipOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(JastipOrderStatus::Diantar);
        $order->update(['delivered_at' => now()]);
        $order->transitionTo(JastipOrderStatus::MenungguKonfirmasi);
    }

    // ── Pelanggan: konfirmasi & bayar COD ─────────────────────────────────────
    public function konfirmasiTerima(JastipOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            // Potong komisi dari saldo mitra
            $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
            if ($mitra->saldo < $order->komisi_zasha) {
                throw new \RuntimeException('Saldo mitra tidak cukup untuk potongan komisi.');
            }
            $mitra->decrement('saldo', $order->komisi_zasha);

            $order->transitionTo(JastipOrderStatus::Selesai);
            $order->update(['selesai_at' => now()]);
        });
    }

    // ── Pelanggan: buka dispute ───────────────────────────────────────────────
    public function bukaDispute(JastipOrder $order, int $pelangganId, string $deskripsi): void
    {
        $this->assertPelanggan($order, $pelangganId);
        $order->transitionTo(JastipOrderStatus::Dispute);

        \App\Models\Dispute::create([
            'order_type'            => 'jastip',
            'order_id'              => $order->id,
            'raised_by_id'          => (string) $pelangganId,
            'raised_by_type'        => 'pelanggan',
            'complaint_description' => $deskripsi,
        ]);
    }

    // ── Admin: selesaikan dispute ────────────────────────────────────────────
    public function selesaikanDispute(JastipOrder $order, string $resolusi): void
    {
        DB::transaction(function () use ($order, $resolusi) {
            if ($resolusi === 'release_to_mitra') {
                $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
                $mitra->decrement('saldo', $order->komisi_zasha);
                $order->transitionTo(JastipOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            } elseif ($resolusi === 'refund_commission') {
                // Komisi tidak ditarik = mitra tidak dipotong
                $order->transitionTo(JastipOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            }
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function hitungJarak(array $stops, float $deliveryLat, float $deliveryLng): array
    {
        $totalKm = 0;
        $stopJarak = [];

        for ($i = 0; $i < count($stops); $i++) {
            if ($i === 0) {
                $stopJarak[] = 0; // titik pertama, dianggap origin
            } else {
                $jarak = $this->haversine(
                    $stops[$i-1]['lat'], $stops[$i-1]['lng'],
                    $stops[$i]['lat'],   $stops[$i]['lng']
                );
                $stopJarak[] = round($jarak, 2);
                $totalKm += $jarak;
            }
        }

        // Stop terakhir → delivery
        $last = end($stops);
        $totalKm += $this->haversine($last['lat'], $last['lng'], $deliveryLat, $deliveryLng);

        return [round($totalKm, 2), $stopJarak];
    }

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    private function assertMitra(JastipOrder $order, int $mitraIdMitra): void
    {
        if ((int) $order->mitra_id !== $mitraIdMitra) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }

    private function assertPelanggan(JastipOrder $order, int $pelangganId): void
    {
        if ((int) $order->pelanggan_id !== $pelangganId) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }
}
