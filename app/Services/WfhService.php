<?php

namespace App\Services;

use App\Enums\WfhOrderStatus;
use App\Models\{EscrowLedger, MitraLayanan, WfhOrder};
use Illuminate\Support\Facades\DB;

class WfhService
{
    // ── Pelanggan: buat order & bayar via saldo ──────────────────────────────
    public function buatOrder(array $data, int $pelangganId): WfhOrder
    {
        return DB::transaction(function () use ($data, $pelangganId) {
            $layanan = MitraLayanan::findOrFail($data['mitra_layanan_id']);

            $unitPrice    = $layanan->tarif_per_unit ?? $layanan->tarif_per_project;
            $quantity     = $data['quantity'] ?? 1;
            $multiplier   = ($data['tipe_order'] === 'express') ? ($layanan->express_multiplier ?? 1.5) : 1;
            $totalPrice   = round($unitPrice * $quantity * $multiplier, 2);
            $komisi       = round($totalPrice * 0.05, 2);
            $pendapatan   = $totalPrice - $komisi;

            // Potong saldo pelanggan
            $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $pelangganId)->lockForUpdate()->firstOrFail();
            if ($pelanggan->saldo < $totalPrice) {
                throw new \RuntimeException('Saldo tidak mencukupi untuk melakukan order.');
            }
            $pelanggan->decrement('saldo', $totalPrice);

            $order = WfhOrder::create([
                'order_code'       => WfhOrder::generateCode(),
                'pelanggan_id'     => $pelangganId,
                'mitra_id'         => $layanan->mitra_id,
                'mitra_layanan_id' => $layanan->id,
                'brief_description'=> $data['brief_description'],
                'reference_url'    => $data['reference_url'] ?? null,
                'quantity'         => $quantity,
                'unit_price'       => $unitPrice,
                'total_price'      => $totalPrice,
                'komisi_zasha'     => $komisi,
                'pendapatan_mitra' => $pendapatan,
                'tipe_order'       => $data['tipe_order'],
                'deadline_at'      => now()->addDays($data['tipe_order'] === 'express' ? 1 : 3),
                'status'           => WfhOrderStatus::MenungguMitra,
                'mitra_notified_at'=> now(),
                'auto_reject_at'   => now()->addMinutes(15),
            ]);

            EscrowLedger::create([
                'wfh_order_id' => $order->id,
                'type'         => 'hold',
                'amount'       => $totalPrice,
                'keterangan'   => 'Dana ditahan saat order dibuat',
                'triggered_by' => 'customer',
            ]);

            return $order;
        });
    }

    // ── Mitra: terima order ───────────────────────────────────────────────────
    public function terimaOrder(WfhOrder $order, int $mitraIdMitra): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(WfhOrderStatus::Dikerjakan);
        $order->update(['mitra_responded_at' => now()]);
    }

    // ── Mitra: tolak order ────────────────────────────────────────────────────
    public function tolakOrder(WfhOrder $order, int $mitraIdMitra, string $alasan): void
    {
        $this->assertMitra($order, $mitraIdMitra);

        DB::transaction(function () use ($order, $alasan) {
            $order->transitionTo(WfhOrderStatus::Ditolak);
            $order->update([
                'alasan_penolakan'  => $alasan,
                'mitra_responded_at'=> now(),
            ]);
            $this->refundPelanggan($order, 'Mitra menolak order');
        });
    }

    // ── Mitra: kirim file hasil kerja ─────────────────────────────────────────
    public function kirimFile(WfhOrder $order, int $mitraIdMitra, string $fileUrl): void
    {
        $this->assertMitra($order, $mitraIdMitra);
        $order->transitionTo(WfhOrderStatus::FileTerkirim);
        $order->update([
            'result_file_url'  => $fileUrl,
            'file_submitted_at'=> now(),
        ]);
        $order->transitionTo(WfhOrderStatus::MenungguKonfirmasi);
        $order->update(['auto_release_at' => now()->addDays(5)]);
    }

    // ── Pelanggan: konfirmasi selesai ─────────────────────────────────────────
    public function konfirmasiSelesai(WfhOrder $order, int $pelangganId): void
    {
        $this->assertPelanggan($order, $pelangganId);

        DB::transaction(function () use ($order) {
            $order->transitionTo(WfhOrderStatus::Selesai);
            $order->update(['selesai_at' => now(), 'trigger_selesai' => 'manual']);
            $this->releaskeEscrow($order, 'manual');
        });
    }

    // ── Pelanggan: buka dispute ───────────────────────────────────────────────
    public function bukaDispute(WfhOrder $order, int $pelangganId, string $deskripsi): void
    {
        $this->assertPelanggan($order, $pelangganId);
        $order->transitionTo(WfhOrderStatus::Dispute);

        \App\Models\Dispute::create([
            'order_type'            => 'wfh',
            'order_id'              => $order->id,
            'raised_by_id'          => (string) $pelangganId,
            'raised_by_type'        => 'pelanggan',
            'complaint_description' => $deskripsi,
        ]);
    }

    // ── Auto-release (dipanggil scheduler) ───────────────────────────────────
    public function autoRelease(WfhOrder $order): void
    {
        if (! $order->isAutoReleaseOverdue()) return;

        DB::transaction(function () use ($order) {
            $order->transitionTo(WfhOrderStatus::Selesai);
            $order->update(['selesai_at' => now(), 'trigger_selesai' => 'auto_release']);
            $this->releaskeEscrow($order, 'auto_release');
        });
    }

    // ── Auto-reject (dipanggil scheduler) ────────────────────────────────────
    public function autoReject(WfhOrder $order): void
    {
        if (! $order->isMitraTimedOut()) return;

        DB::transaction(function () use ($order) {
            $order->transitionTo(WfhOrderStatus::Ditolak);
            $order->update(['alasan_penolakan' => 'Mitra tidak merespons dalam 15 menit']);
            $this->refundPelanggan($order, 'Auto-reject karena timeout mitra');
        });
    }

    // ── Admin: selesaikan dispute ─────────────────────────────────────────────
    public function selesaikanDispute(WfhOrder $order, string $resolusi, float $refundAmount = 0): void
    {
        DB::transaction(function () use ($order, $resolusi, $refundAmount) {
            if ($resolusi === 'refund_to_customer') {
                $order->transitionTo(WfhOrderStatus::Refund);
                $this->refundPelanggan($order, 'Dispute diselesaikan: refund ke pelanggan');
            } elseif ($resolusi === 'release_to_mitra') {
                $order->transitionTo(WfhOrderStatus::Selesai);
                $order->update(['selesai_at' => now(), 'trigger_selesai' => 'dispute_resolved']);
                $this->releaskeEscrow($order, 'dispute');
            }
        });
    }

    // ── Private helpers ───────────────────────────────────────────────────────
    private function releaskeEscrow(WfhOrder $order, string $trigger): void
    {
        $mitra = \App\Models\Mitra::where('id_mitra', $order->mitra_id)->lockForUpdate()->firstOrFail();
        $mitra->increment('saldo', $order->pendapatan_mitra);

        EscrowLedger::create([
            'wfh_order_id' => $order->id,
            'type'         => 'release_to_mitra',
            'amount'       => $order->pendapatan_mitra,
            'keterangan'   => 'Pembayaran ke mitra',
            'triggered_by' => $trigger,
        ]);

        EscrowLedger::create([
            'wfh_order_id' => $order->id,
            'type'         => 'commission',
            'amount'       => $order->komisi_zasha,
            'keterangan'   => 'Komisi Zasha 5%',
            'triggered_by' => $trigger,
        ]);
    }

    private function refundPelanggan(WfhOrder $order, string $keterangan): void
    {
        \App\Models\Pelanggan::where('id_pelanggan', $order->pelanggan_id)
            ->lockForUpdate()->firstOrFail()
            ->increment('saldo', $order->total_price);

        EscrowLedger::create([
            'wfh_order_id' => $order->id,
            'type'         => 'refund_to_customer',
            'amount'       => $order->total_price,
            'keterangan'   => $keterangan,
            'triggered_by' => 'admin',
        ]);
    }

    private function assertMitra(WfhOrder $order, int $mitraIdMitra): void
    {
        if ((int) $order->mitra_id !== $mitraIdMitra) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }

    private function assertPelanggan(WfhOrder $order, int $pelangganId): void
    {
        if ((int) $order->pelanggan_id !== $pelangganId) {
            throw new \RuntimeException('Anda tidak berhak mengakses order ini.');
        }
    }
}
