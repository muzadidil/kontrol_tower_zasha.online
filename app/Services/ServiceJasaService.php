<?php

namespace App\Services;

use App\Enums\ServiceOrderStatus;
use App\Models\{Mitra, MitraLayanan, ServiceOrder, ServiceOrderItem};
use Illuminate\Support\Facades\DB;

class ServiceJasaService
{
    public const KOMISI_RATE = 0.05;
    public const TARIF_BENSIN_PER_KM = 2000;

    public function buatOrder(array $data, int $pelangganId): ServiceOrder
    {
        return DB::transaction(function () use ($data, $pelangganId) {
            $layanan = MitraLayanan::with('mitra')->findOrFail($data['mitra_layanan_id']);
            $biayaBensin = round(($data['jarak_km'] ?? 0) * ($layanan->tarif_bensin_per_km ?? self::TARIF_BENSIN_PER_KM), 0);
            $estimasiAwal = $layanan->biaya_service_standar + $biayaBensin;

            $mitra = Mitra::where('id_mitra', $layanan->mitra_id)->first();

            return ServiceOrder::create([
                'order_code'            => ServiceOrder::generateCode(),
                'pelanggan_id'          => $pelangganId,
                'mitra_id'              => $layanan->mitra_id,
                'mitra_layanan_id'      => $layanan->id,
                'biaya_service_standar' => $layanan->biaya_service_standar,
                'jarak_km'              => $data['jarak_km'] ?? 0,
                'biaya_bensin'          => $biayaBensin,
                'estimasi_awal'         => $estimasiAwal,
                'metode_pembayaran'     => null,
                'saldo_mitra_snapshot'  => $mitra?->saldo ?? 0,
                'cod_eligible'          => true,
                'alamat_pelanggan'      => $data['alamat_pelanggan'],
                'pelanggan_lat'         => $data['pelanggan_lat'] ?? null,
                'pelanggan_lng'         => $data['pelanggan_lng'] ?? null,
                'keluhan_pelanggan'     => $data['keluhan_pelanggan'] ?? null,
                'status'                => ServiceOrderStatus::MenungguMitra,
                'mitra_notified_at'     => now(),
                'auto_reject_at'        => now()->addMinutes(15),
            ]);
        });
    }

    public function terimaOrder(ServiceOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(ServiceOrderStatus::MenujuLokasi);
        $order->update(['mitra_responded_at' => now(), 'berangkat_at' => now()]);
    }

    public function tolakOrder(ServiceOrder $order, int $mitraIdMitra, string $alasan): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(ServiceOrderStatus::Ditolak);
        $order->update(['alasan_penolakan' => $alasan, 'mitra_responded_at' => now()]);
    }

    public function mulaiDiagnosa(ServiceOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(ServiceOrderStatus::Diagnosa);
    }

    public function submitDiagnosa(ServiceOrder $order, int $mitraIdMitra, array $items): void
    {
        $this->assertMitra($order, $mitraIdMitra);

        DB::transaction(function () use ($order, $items) {
            $totalJasa = 0;
            $totalSparepart = 0;

            foreach ($items as $itemData) {
                ServiceOrderItem::create([
                    'service_order_id' => $order->id,
                    'tipe'             => $itemData['tipe'],
                    'nama_item'        => $itemData['nama_item'],
                    'harga'            => $itemData['harga'],
                    'catatan'          => $itemData['catatan'] ?? null,
                ]);

                if ($itemData['tipe'] === 'jasa') {
                    $totalJasa += $itemData['harga'];
                } else {
                    $totalSparepart += $itemData['harga'];
                }
            }

            $totalBiaya = $totalJasa + $totalSparepart + $order->biaya_bensin;
            $komisi = round($totalJasa * self::KOMISI_RATE, 0); // hanya jasa kena komisi
            $pendapatan = $totalBiaya - $komisi;

            $order->update([
                'total_jasa'       => $totalJasa,
                'total_sparepart'  => $totalSparepart,
                'total_biaya'      => $totalBiaya,
                'komisi_zasha'     => $komisi,
                'pendapatan_mitra' => $pendapatan,
            ]);

            $order->transitionTo(ServiceOrderStatus::MenungguKonfirmasiHarga);
        });
    }

    public function approveHarga(ServiceOrder $order, int $pelangganId, string $metodePembayaran): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order, $metodePembayaran) {
            // Validasi saldo jika pakai saldo
            if ($metodePembayaran === 'saldo') {
                $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                    ->lockForUpdate()->firstOrFail();
                if ($pelanggan->saldo < $order->total_biaya) {
                    throw new \RuntimeException('Saldo tidak cukup.');
                }
                $pelanggan->decrement('saldo', $order->total_biaya);
            }

            $order->update(['metode_pembayaran' => $metodePembayaran]);
            $order->transitionTo(ServiceOrderStatus::Dikerjakan);
            $order->update(['mulai_kerja_at' => now()]);
        });
    }

    public function selesaiKerja(ServiceOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(ServiceOrderStatus::MenungguKonfirmasi);
    }

    public function konfirmasiTerima(ServiceOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();

            if ($order->metode_pembayaran === 'cod') {
                if ($mitra->saldo < $order->komisi_zasha) {
                    throw new \RuntimeException('Saldo mitra tidak cukup.');
                }
                $mitra->decrement('saldo', $order->komisi_zasha);
            } else {
                $mitra->increment('saldo', $order->pendapatan_mitra);
            }

            $order->transitionTo(ServiceOrderStatus::Selesai);
            $order->update(['selesai_at' => now()]);
        });
    }

    public function bukaDispute(ServiceOrder $order, int $pelangganId, string $deskripsi): void
    {
        $this->assertPelanggan($order, $pelangganId);
        $order->transitionTo(ServiceOrderStatus::Dispute);

        \App\Models\Dispute::create([
            'order_type'            => 'service',
            'order_id'              => $order->id,
            'raised_by_id'          => (string) $pelangganId,
            'raised_by_type'        => 'pelanggan',
            'complaint_description' => $deskripsi,
        ]);
    }

    public function selesaikanDispute(ServiceOrder $order, string $resolusi): void
    {
        DB::transaction(function () use ($order, $resolusi) {
            if ($resolusi === 'release_to_mitra') {
                $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
                if ($order->metode_pembayaran === 'cod') {
                    $mitra->decrement('saldo', $order->komisi_zasha);
                } else {
                    $mitra->increment('saldo', $order->pendapatan_mitra);
                }
            } elseif ($resolusi === 'refund_to_customer' && $order->metode_pembayaran === 'saldo') {
                \App\Models\Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                    ->increment('saldo', $order->total_biaya);
            }

            $order->transitionTo(ServiceOrderStatus::Selesai);
            $order->update(['selesai_at' => now()]);
        });
    }

    private function assertMitra(ServiceOrder $order, int $mitraIdMitra): void
    {
        if ((int) $order->mitra_id !== $mitraIdMitra) {
            throw new \RuntimeException('Anda tidak berhak.');
        }
    }

    private function assertPelanggan(ServiceOrder $order, int $pelangganId): void
    {
        if ((int) $order->pelanggan_id !== $pelangganId) {
            throw new \RuntimeException('Anda tidak berhak.');
        }
    }
}
