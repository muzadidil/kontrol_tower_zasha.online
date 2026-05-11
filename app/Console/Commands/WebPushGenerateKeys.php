<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;
use App\Models\Setting;

class WebPushGenerateKeys extends Command
{
    protected $signature = 'web-push:generate-keys';
    protected $description = 'Generate VAPID keys untuk Web Push Notifications';

    public function handle()
    {
        try {
            $vapidKeys = VAPID::createVapidKeys();

            Setting::set('vapid_public_key', $vapidKeys['publicKey']);
            Setting::set('vapid_private_key', $vapidKeys['privateKey']);

            $this->info('✓ VAPID keys generated dan disimpan ke settings table');
            $this->line('Public Key: ' . substr($vapidKeys['publicKey'], 0, 50) . '...');
            $this->line('Private Key: ' . substr($vapidKeys['privateKey'], 0, 50) . '...');
        } catch (\Exception $e) {
            $this->error('✗ Gagal generate VAPID keys: ' . $e->getMessage());
        }
    }
}
