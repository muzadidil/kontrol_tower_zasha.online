<?php

namespace App\Services;

use App\Enums\TenagaOrderStatus;
use App\Models\{Mitra, MitraLayanan, TenagaOrder};
use Illuminate\Support\Facades\DB;

class TenagaService
{
    public const KOMISI_RATE = 0.05;

    public function buatOrder(array $data, int $pelangganId): TenagaOrder
    {
        return DB::transaction(function () use ($data, $pelangganId) {
            $layanan = MitraLayanan::with('mitra')->findOrFail($data['mitra_layanan_id']);

            $tarif = $data['tipe_durasi'] === 'jam'
                ? $layanan->tarif_per_jam
                : $layanan->tarif_per_hari;

            $totalBiaya  = round($tarif * $data['durasi'], 0);
            $komisi      = round($totalBiaya * self::KOMISI_RATE, 0);
            $pendapatan  = $totalBiaya - $komisi;

            $mitra = Mitra::where('id_mitra', $layanan->mitra_id)->first();
            $codEligible = $data['metode_pembayaran'] === 'cod'
                ? $mitra && $mitra->saldo >= $komisi
                : true;

            // Saldo / Transfer: potong saldo pelanggan up-front
            if (in_array($data['metode_pembayaran'], ['saldo', 'transfer'])) {
                $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $pelangganId)->lockForUpdate()->firstOrFail();
                if ($data['metode_pembayaran'] === 'saldo' && $pelanggan->saldo < $totalBiaya) {
                    throw new \RuntimeException('Saldo tidak mencukupi.');
                }
                if ($data['metode_pembayaran'] === 'saldo') {
                    $pelanggan->decrement('saldo', $totalBiaya);
                }
            }

            return TenagaOrder::create([
                'order_code'           => TenagaOrder::generateCode(),
                'pelanggan_id'         => $pelangganId,
                'mitra_id'             => $layanan->mitra_id,
                'mitra_layanan_id'     => $layanan->id,
                'tipe_waktu'           => $data['tipe_waktu'],
                'jadwal_at'            => $data['jadwal_at'] ?? null,
                'tipe_durasi'          => $data['tipe_durasi'],
                'durasi'               => $data['durasi'],
                'tarif'                => $tarif,
                'total_biaya'          => $totalBiaya,
                'komisi_zasha'         => $komisi,
                'pendapatan_mitra'     => $pendapatan,
                'metode_pembayaran'    => $data['metode_pembayaran'],
                'saldo_mitra_snapshot' => $mitra?->saldo ?? 0,
                'cod_eligible'         => $codEligible,
                'alamat_pelanggan'     => $data['alamat_pelanggan'],
                'pelanggan_lat'        => $data['pelanggan_lat'] ?? null,
                'pelanggan_lng'        => $data['pelanggan_lng'] ?? null,
                'keterangan_kerja'     => $data['keterangan_kerja'] ?? null,
                'status'               => TenagaOrderStatus::MenungguMitra,
                'mitra_notified_at'    => now(),
                'auto_reject_at'       => now()->addMinutes(15),
            ]);
        });
    }

    public function terimaOrder(TenagaOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(TenagaOrderStatus::MenujuLokasi);
        $order->update(['mitra_responded_at' => now(), 'berangkat_at' => now()]);
    }

    public function tolakOrder(TenagaOrder $order, int $mitraIdMitra, string $alasan): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        DB::transaction(function () use ($order, $alasan) {
            $order->transitionTo(TenagaOrderStatus::Ditolak);
            $order->update(['alasan_penolakan' => $alasan, 'mitra_responded_at' => now()]);

            // Refund jika sudah bayar saldo
            if ($order->metode_pembayaran === 'saldo') {
                \App\Models\Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                    ->increment('saldo', $order->total_biaya);
            }
        });
    }

    public function mulaiKerja(TenagaOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(TenagaOrderStatus::Dikerjakan);
        $order->update(['mulai_kerja_at' => now()]);
    }

    public function selesaiKerja(TenagaOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(TenagaOrderStatus::MenungguKonfirmasi);
    }

    public function konfirmasiTerima(TenagaOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();

            if ($order->metode_pembayaran === 'cod') {
                // COD: potong komisi dari mitra
                if ($mitra->saldo < $order->komisi_zasha) {
                    throw new \RuntimeException('Saldo mitra tidak cukup untuk komisi.');
                }
                $mitra->decrement('saldo', $order->komisi_zasha);
            } else {
                // Saldo/Transfer: tambah pendapatan ke mitra
                $mitra->increment('saldo', $order->pendapatan_mitra);
            }

            $order->transitionTo(TenagaOrderStatus::Selesai);
            $order->update(['selesai_at' => now()]);
        });
    }

    public function bukaDispute(TenagaOrder $order, int $pelangganId, string $deskripsi): void
    {
        $this->assertPelanggan($order, $pelangganId);
        $order->transitionTo(TenagaOrderStatus::Dispute);

        \App\Models\Dispute::create([
            'order_type'            => 'tenaga',
            'order_id'              => $order->id,
            'raised_by_id'          => (string) $pelangganId,
            'raised_by_type'        => 'pelanggan',
            'complaint_description' => $deskripsi,
        ]);
    }

    public function selesaikanDispute(TenagaOrder $order, string $resolusi): void
    {
        DB::transaction(function () use ($order, $resolusi) {
            if ($resolusi === 'release_to_mitra') {
                $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
                if ($order->metode_pembayaran === 'cod') {
                    $mitra->decrement('saldo', $order->komisi_zasha);
                } else {
                    $mitra->increment('saldo', $order->pendapatan_mitra);
                }
                $order->transitionTo(TenagaOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            } elseif ($resolusi === 'refund_to_customer') {
                if ($order->metode_pembayaran === 'saldo') {
                    \App\Models\Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                        ->increment('saldo', $order->total_biaya);
                }
                $order->transitionTo(TenagaOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            }
        });
    }

    private function assertMitra(TenagaOrder $order, int $mitraIdMitra): void
    {
        if ((int) $order->mitra_id !== $mitraIdMitra) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }

    private function assertPelanggan(TenagaOrder $order, int $pelangganId): void
    {
        if ((int) $order->pelanggan_id !== $pelangganId) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }
}
