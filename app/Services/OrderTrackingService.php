<?php

namespace App\Services;

use App\Models\OrderTracking;
use App\Models\MitraNotifikasi;
use App\Models\Mitra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PushNotificationService;

class OrderTrackingService
{
    public function createTracking(
        string $orderType,
        int $orderId,
        int $mitraId,
        int $pelangganId,
        float $hargaJual,
        float $hargaModal
    ): OrderTracking {
        try {
            return DB::transaction(function () use ($orderType, $orderId, $mitraId, $pelangganId, $hargaJual, $hargaModal) {
                $komisiZasha = $hargaJual - $hargaModal;

                $tracking = OrderTracking::create([
                    'order_type' => $orderType,
                    'order_id' => $orderId,
                    'mitra_id' => $mitraId,
                    'pelanggan_id' => $pelangganId,
                    'status' => 'pending',
                    'harga_jual' => $hargaJual,
                    'harga_modal' => $hargaModal,
                    'komisi_zasha' => $komisiZasha,
                    'escrow_status' => 'held',
                ]);

                $notif = MitraNotifikasi::create([
                    'mitra_id' => $mitraId,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_masuk',
                    'judul' => "Order baru: {$orderType}",
                    'pesan' => "Anda menerima order baru type {$orderType} senilai Rp " . number_format($hargaJual, 0, ',', '.'),
                ]);

                // Send push notification
                try {
                    $pushService = new PushNotificationService();
                    $pushService->sendToMitra(
                        $mitraId,
                        "Order Masuk! 🔔",
                        "Ada pesanan baru dari pelanggan. Buka untuk melihat detail.",
                        route('mitra.dashboard'),
                        'order-masuk'
                    );
                } catch (\Exception $e) {
                    Log::warning('Push notification failed for order ' . $tracking->id, [
                        'error' => $e->getMessage(),
                    ]);
                }

                return $tracking;
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::createTracking failed', [
                'message' => $e->getMessage(),
                'order_type' => $orderType,
                'mitra_id' => $mitraId,
                'pelanggan_id' => $pelangganId,
            ]);
            throw $e;
        }
    }

    public function mitraAccept(OrderTracking $tracking): void
    {
        try {
            DB::transaction(function () use ($tracking) {
                if ($tracking->status !== 'pending') {
                    throw new \RuntimeException('Order harus dalam status pending untuk diterima.');
                }

                $tracking->update(['status' => 'accepted']);

                Mitra::where('id_mitra', $tracking->mitra_id)
                    ->update(['status_online' => 'sibuk']);

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Order diterima',
                    'pesan' => 'Anda telah menerima order. Status Anda berubah menjadi Sibuk.',
                ]);

                // Notifikasi ke pelanggan
                \App\Helpers\NotifHelper::kirim(
                    $tracking->pelanggan_id,
                    'Order Diterima ✓',
                    'Mitra sedang menuju lokasimu!',
                    'pesanan'
                );
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::mitraAccept failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
                'mitra_id' => $tracking->mitra_id,
            ]);
            throw $e;
        }
    }

    public function mitraReject(OrderTracking $tracking, string $pesan): void
    {
        try {
            DB::transaction(function () use ($tracking, $pesan) {
                if ($tracking->status !== 'pending') {
                    throw new \RuntimeException('Order harus dalam status pending untuk ditolak.');
                }

                $tracking->update([
                    'status' => 'ditolak_mitra',
                    'pesan_tolak' => $pesan,
                ]);

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Order ditolak',
                    'pesan' => 'Anda telah menolak order dengan alasan: ' . $pesan,
                ]);

                // Notifikasi ke pelanggan
                \App\Helpers\NotifHelper::kirim(
                    $tracking->pelanggan_id,
                    'Order Ditolak ✗',
                    'Maaf, mitra tidak bisa mengambil ordermu. Alasan: ' . $pesan,
                    'pesanan'
                );
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::mitraReject failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
                'mitra_id' => $tracking->mitra_id,
            ]);
            throw $e;
        }
    }

    public function updateProgress(OrderTracking $tracking, string $newStatus): void
    {
        try {
            DB::transaction(function () use ($tracking, $newStatus) {
                $validTransitions = [
                    'pending' => ['accepted'],
                    'accepted' => ['menuju_lokasi'],
                    'menuju_lokasi' => ['di_lokasi'],
                    'di_lokasi' => ['dikerjakan'],
                    'dikerjakan' => ['selesai_mitra'],
                    'selesai_mitra' => ['selesai', 'belum_selesai'],
                    'belum_selesai' => ['dikerjakan', 'selesai_mitra'],
                ];

                if (!isset($validTransitions[$tracking->status]) || !in_array($newStatus, $validTransitions[$tracking->status])) {
                    throw new \RuntimeException("Transisi dari {$tracking->status} ke {$newStatus} tidak valid.");
                }

                $tracking->update(['status' => $newStatus]);

                $statusLabels = [
                    'menuju_lokasi' => 'Menuju Lokasi',
                    'di_lokasi' => 'Tiba di Lokasi',
                    'dikerjakan' => 'Sedang Dikerjakan',
                    'selesai_mitra' => 'Selesai (menunggu konfirmasi)',
                ];

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Status order diupdate',
                    'pesan' => 'Status order berubah menjadi: ' . ($statusLabels[$newStatus] ?? $newStatus),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::updateProgress failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
                'new_status' => $newStatus,
            ]);
            throw $e;
        }
    }

    public function pelangganConfirmSelesai(OrderTracking $tracking): void
    {
        try {
            DB::transaction(function () use ($tracking) {
                if ($tracking->status !== 'selesai_mitra') {
                    throw new \RuntimeException('Order harus dalam status selesai_mitra untuk dikonfirmasi.');
                }

                $tracking->update([
                    'status' => 'selesai',
                    'escrow_status' => 'released',
                ]);

                Mitra::where('id_mitra', $tracking->mitra_id)
                    ->lockForUpdate()
                    ->increment('saldo', $tracking->harga_modal);

                Mitra::where('id_mitra', $tracking->mitra_id)
                    ->update(['status_online' => 'online']);

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Order selesai & dibayar',
                    'pesan' => 'Pelanggan mengonfirmasi order selesai. Saldo Rp ' . number_format($tracking->harga_modal, 0, ',', '.') . ' telah ditransfer ke akun Anda.',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::pelangganConfirmSelesai failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
            ]);
            throw $e;
        }
    }

    public function pelangganBelumSelesai(OrderTracking $tracking): void
    {
        try {
            DB::transaction(function () use ($tracking) {
                if ($tracking->status !== 'selesai_mitra') {
                    throw new \RuntimeException('Order harus dalam status selesai_mitra untuk ditandai belum selesai.');
                }

                $tracking->update(['status' => 'belum_selesai']);

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Order belum selesai',
                    'pesan' => 'Pelanggan menginformasikan bahwa pekerjaan belum selesai. Silakan lanjutkan perbaikan.',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::pelangganBelumSelesai failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
            ]);
            throw $e;
        }
    }

    public function mitraPerbaikiSelesai(OrderTracking $tracking): void
    {
        try {
            DB::transaction(function () use ($tracking) {
                if ($tracking->status !== 'belum_selesai') {
                    throw new \RuntimeException('Order harus dalam status belum_selesai untuk diperbaiki.');
                }

                $tracking->update(['status' => 'selesai_mitra']);

                MitraNotifikasi::create([
                    'mitra_id' => $tracking->mitra_id,
                    'tracking_id' => $tracking->id,
                    'tipe' => 'order_update',
                    'judul' => 'Perbaikan selesai',
                    'pesan' => 'Anda telah menandai order sebagai selesai kembali. Menunggu konfirmasi pelanggan.',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('OrderTrackingService::mitraPerbaikiSelesai failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $tracking->id,
            ]);
            throw $e;
        }
    }
}
