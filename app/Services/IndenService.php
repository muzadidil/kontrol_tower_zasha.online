<?php

namespace App\Services;

use App\Enums\IndenOrderStatus;
use App\Models\{IndenOrder, Mitra, Pelanggan};
use Illuminate\Support\Facades\DB;

class IndenService
{
    public const KOMISI_RATE = 0.05;
    public const DP_RATE = 0.50;

    public function buatOrder(array $data, int $pelangganId): IndenOrder
    {
        return DB::transaction(function () use ($data, $pelangganId) {
            $tarif = $data['tipe_durasi'] === 'jam'
                ? $data['tarif_per_jam']
                : $data['tarif_per_hari'];

            $totalBiaya    = round($tarif * $data['durasi'], 0);
            $dpAmount      = round($totalBiaya * self::DP_RATE, 0);
            $pelunasanAmount = $totalBiaya - $dpAmount;
            $komisi        = round($totalBiaya * self::KOMISI_RATE, 0);
            $pendapatan    = $totalBiaya - $komisi;

            return IndenOrder::create([
                'order_code'           => IndenOrder::generateCode(),
                'pelanggan_id'         => $pelangganId,
                'mitra_id'             => $data['mitra_id'],
                'tanggal_pelaksanaan'  => $data['tanggal_pelaksanaan'],
                'tipe_durasi'          => $data['tipe_durasi'],
                'durasi'               => $data['durasi'],
                'tarif'                => $tarif,
                'total_biaya'          => $totalBiaya,
                'dp_amount'            => $dpAmount,
                'pelunasan_amount'     => $pelunasanAmount,
                'komisi_zasha'         => $komisi,
                'pendapatan_mitra'     => $pendapatan,
                'metode_pembayaran'    => $data['metode_pembayaran'],
                'alamat_pelanggan'     => $data['alamat_pelanggan'],
                'pelanggan_lat'        => $data['pelanggan_lat'] ?? null,
                'pelanggan_lng'        => $data['pelanggan_lng'] ?? null,
                'keterangan_kerja'     => $data['keterangan_kerja'] ?? null,
                'status'               => IndenOrderStatus::MenungguMitra,
                'auto_reject_at'       => now()->addMinutes(15),
            ]);
        });
    }

    public function approveOrder(IndenOrder $order, int $mitraId): void
    {
        $this->assertMitra($order, $mitraId);
        $order->transitionTo(IndenOrderStatus::MenungguDp);
        $order->update(['mitra_responded_at' => now()]);
    }

    public function tolakOrder(IndenOrder $order, int $mitraId, string $alasan): void
    {
        $this->assertMitra($order, $mitraId);
        DB::transaction(function () use ($order, $alasan) {
            $order->transitionTo(IndenOrderStatus::Ditolak);
            $order->update(['alasan_penolakan' => $alasan, 'mitra_responded_at' => now()]);

            // Refund jika sudah bayar DP
            if ($order->dp_paid_at && $order->metode_pembayaran === 'saldo') {
                Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                    ->increment('saldo', $order->dp_amount);
            }
        });
    }

    public function bayarDp(IndenOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $pelanggan = Pelanggan::where('id_pelanggan', $order->pelanggan_id)->lockForUpdate()->firstOrFail();

            if ($order->metode_pembayaran === 'saldo') {
                if ($pelanggan->saldo < $order->dp_amount) {
                    throw new \RuntimeException('Saldo tidak mencukupi untuk pembayaran DP.');
                }
                $pelanggan->decrement('saldo', $order->dp_amount);
            }

            $order->transitionTo(IndenOrderStatus::DpDibayar);
            $order->update(['dp_paid_at' => now()]);
        });
    }

    public function mitraTiba(IndenOrder $order, int $mitraId): void
    {
        $this->assertMitra($order, $mitraId);
        $order->transitionTo(IndenOrderStatus::Dikerjakan);
    }

    public function bayarPelunasan(IndenOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $pelanggan = Pelanggan::where('id_pelanggan', $order->pelanggan_id)->lockForUpdate()->firstOrFail();

            if ($order->metode_pembayaran === 'saldo') {
                if ($pelanggan->saldo < $order->pelunasan_amount) {
                    throw new \RuntimeException('Saldo tidak mencukupi untuk pembayaran pelunasan.');
                }
                $pelanggan->decrement('saldo', $order->pelunasan_amount);
            }

            $order->update(['pelunasan_paid_at' => now()]);
            $order->transitionTo(IndenOrderStatus::MenungguPelunasan);
            $order->update(['auto_konfirmasi_at' => now()->addHours(24)]);
        });
    }

    public function konfirmasiSelesai(IndenOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();

            if ($order->metode_pembayaran === 'cod') {
                if ($mitra->saldo < $order->komisi_zasha) {
                    throw new \RuntimeException('Saldo mitra tidak cukup untuk komisi.');
                }
                $mitra->decrement('saldo', $order->komisi_zasha);
            } else {
                $mitra->increment('saldo', $order->pendapatan_mitra);
            }

            $order->transitionTo(IndenOrderStatus::Selesai);
            $order->update(['selesai_at' => now()]);
        });
    }

    public function bukaDispute(IndenOrder $order, int $pelangganId, string $deskripsi): void
    {
        $this->assertPelanggan($order, $pelangganId);
        $order->transitionTo(IndenOrderStatus::Dispute);

        \App\Models\Dispute::create([
            'order_type'            => 'inden',
            'order_id'              => $order->id,
            'raised_by_id'          => (string) $pelangganId,
            'raised_by_type'        => 'pelanggan',
            'complaint_description' => $deskripsi,
        ]);
    }

    public function selesaikanDispute(IndenOrder $order, string $resolusi): void
    {
        DB::transaction(function () use ($order, $resolusi) {
            if ($resolusi === 'release_to_mitra') {
                $mitra = Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
                if ($order->metode_pembayaran === 'cod') {
                    $mitra->decrement('saldo', $order->komisi_zasha);
                } else {
                    $mitra->increment('saldo', $order->pendapatan_mitra);
                }
                $order->transitionTo(IndenOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            } elseif ($resolusi === 'refund_to_customer') {
                $refundAmount = $order->dp_amount;
                if ($order->pelunasan_paid_at) {
                    $refundAmount += $order->pelunasan_amount;
                }
                if ($order->metode_pembayaran === 'saldo') {
                    Pelanggan::where('id_pelanggan', $order->pelanggan_id)
                        ->increment('saldo', $refundAmount);
                }
                $order->transitionTo(IndenOrderStatus::Selesai);
                $order->update(['selesai_at' => now()]);
            }
        });
    }

    private function assertMitra(IndenOrder $order, int $mitraId): void
    {
        if ((int) $order->mitra_id !== $mitraId) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }

    private function assertPelanggan(IndenOrder $order, int $pelangganId): void
    {
        if ((int) $order->pelanggan_id !== $pelangganId) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }
}
