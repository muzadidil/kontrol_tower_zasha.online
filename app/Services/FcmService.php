<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log};

/**
 * Firebase Cloud Messaging Service
 *
 * Stub implementation. Untuk production, install kreait/firebase-php
 * atau pakai HTTP v1 API dengan service account credentials.
 */
class FcmService
{
    protected ?string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    /**
     * Kirim push notification ke 1 device.
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (! $this->serverKey || ! $fcmToken) {
            Log::info('FCM skipped (no key or token)', compact('fcmToken', 'title'));
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM send error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Notifikasi order baru ke mitra.
     */
    public function notifyMitraNewOrder(\App\Models\Mitra $mitra, string $orderCode, string $modul): void
    {
        $this->sendToToken(
            $mitra->fcm_token ?? '',
            'Order Baru: ' . strtoupper($modul),
            "Anda menerima order baru ($orderCode). Buka aplikasi untuk respon dalam 15 menit.",
            ['order_code' => $orderCode, 'modul' => $modul, 'type' => 'new_order']
        );
    }

    /**
     * Notifikasi update status order ke pelanggan.
     */
    public function notifyPelangganOrderUpdate(\App\Models\Pelanggan $pelanggan, string $orderCode, string $status): void
    {
        $this->sendToToken(
            $pelanggan->fcm_token ?? '',
            'Update Order ' . $orderCode,
            'Status order Anda: ' . ucwords(str_replace('_', ' ', $status)),
            ['order_code' => $orderCode, 'status' => $status, 'type' => 'order_update']
        );
    }
}
