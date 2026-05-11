<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class WebPushGenerateKeys extends Command
{
    protected $signature = 'web-push:generate-keys';
    protected $description = 'Generate VAPID keys untuk Web Push Notifications';

    public function handle()
    {
        $this->info('Generating VAPID keys...');

        try {
            // Try using openssl to generate EC P-256 keypair
            $config = [
                'private_key_bits' => 256,
                'private_key_type' => OPENSSL_KEYTYPE_EC,
                'curve_name' => 'prime256v1',
            ];

            $resource = openssl_pkey_new($config);
            openssl_pkey_export($resource, $privateKeyPem);

            $details = openssl_pkey_get_details($resource);
            $publicKeyPem = $details['key'];

            // Extract raw keys from PEM format
            $privateKeyData = $this->extractEcKey($privateKeyPem, 'PRIVATE');
            $publicKeyData = $this->extractEcKey($publicKeyPem, 'PUBLIC');

            $publicKeyBase64 = rtrim(strtr(base64_encode($publicKeyData), '+/', '-_'), '=');
            $privateKeyBase64 = rtrim(strtr(base64_encode($privateKeyData), '+/', '-_'), '=');

            Setting::set('vapid_public_key', $publicKeyBase64);
            Setting::set('vapid_private_key', $privateKeyBase64);

            $this->info('✓ VAPID keys generated dan disimpan ke settings table');
            $this->info('Public Key: ' . substr($publicKeyBase64, 0, 60) . '...');
            $this->info('Private Key: ' . substr($privateKeyBase64, 0, 60) . '...');
        } catch (\Exception $e) {
            $this->error('✗ Gagal generate VAPID keys: ' . $e->getMessage());
            $this->info('');
            $this->warn('Alternatif: Gunakan tools online untuk generate keys:');
            $this->info('https://web-push-codelab.glitch.me/');
            $this->info('');
            $this->info('Kemudian set di settings table:');
            $this->line("Setting::set('vapid_public_key', 'YOUR_PUBLIC_KEY');");
            $this->line("Setting::set('vapid_private_key', 'YOUR_PRIVATE_KEY');");

            return 1;
        }

        return 0;
    }

    private function extractEcKey($pem, $type)
    {
        $pattern = ($type === 'PRIVATE')
            ? '/-----BEGIN EC PRIVATE KEY-----(.+?)-----END EC PRIVATE KEY-----/s'
            : '/-----BEGIN PUBLIC KEY-----(.+?)-----END PUBLIC KEY-----/s';

        if (preg_match($pattern, $pem, $matches)) {
            return base64_decode($matches[1]);
        }

        throw new \Exception('Could not extract EC key from PEM');
    }
}
