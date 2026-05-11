<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $publicKey = Setting::get('vapid_public_key');
        $privateKey = Setting::get('vapid_private_key');

        if (!$publicKey || !$privateKey) {
            Log::warning('VAPID keys not configured. Run: php artisan web-push:generate-keys');
        }

        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:' . config('app.name'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
    }

    public function sendToMitra(int $mitraId, string $title, string $body, string $url = '', string $tag = 'zasha-notification')
    {
        $subscriptions = PushSubscription::where('user_type', 'mitra')
            ->where('user_id', $mitraId)
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::info('No push subscriptions for mitra ' . $mitraId);
            return 0;
        }

        $sent = 0;
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => $tag,
            'requireInteraction' => true,
        ]);

        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth_key,
                ]);

                $this->webPush->sendOneNotification(
                    $subscription,
                    $payload
                );

                $sent++;
                Log::info('Push sent to mitra ' . $mitraId . ' via endpoint');
            } catch (\Exception $e) {
                Log::error('Push failed for mitra ' . $mitraId, [
                    'endpoint' => $sub->endpoint,
                    'error' => $e->getMessage(),
                ]);

                // Hapus subscription kalau endpoint expired
                if (str_contains($e->getMessage(), 'expired') || str_contains($e->getMessage(), '410')) {
                    $sub->delete();
                    Log::info('Deleted expired subscription for mitra ' . $mitraId);
                }
            }
        }

        return $sent;
    }

    public function sendToPelanggan(int $pelangganId, string $title, string $body, string $url = '', string $tag = 'zasha-notification')
    {
        $subscriptions = PushSubscription::where('user_type', 'pelanggan')
            ->where('user_id', $pelangganId)
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $sent = 0;
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => $tag,
            'requireInteraction' => false,
        ]);

        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth_key,
                ]);

                $this->webPush->sendOneNotification(
                    $subscription,
                    $payload
                );

                $sent++;
            } catch (\Exception $e) {
                Log::error('Push failed for pelanggan ' . $pelangganId, [
                    'error' => $e->getMessage(),
                ]);

                if (str_contains($e->getMessage(), 'expired') || str_contains($e->getMessage(), '410')) {
                    $sub->delete();
                }
            }
        }

        return $sent;
    }
}
