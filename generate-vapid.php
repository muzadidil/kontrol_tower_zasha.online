<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Minishlink\WebPush\VAPID;
use App\Models\Setting;

try {
    $vapidKeys = VAPID::createVapidKeys();

    Setting::set('vapid_public_key', $vapidKeys['publicKey']);
    Setting::set('vapid_private_key', $vapidKeys['privateKey']);

    echo "✓ VAPID keys generated successfully!\n";
    echo "Public Key: " . substr($vapidKeys['publicKey'], 0, 50) . "...\n";
    echo "Private Key: " . substr($vapidKeys['privateKey'], 0, 50) . "...\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
