# Browser Push Notification Setup Guide

## Overview

Sistem push notification untuk mitra menggunakan Web Push API dengan Service Worker. Push notifications adalah suplemen dari polling system (polling tetap jadi fallback).

## Architecture

```
Mitra Browser
    ↓
1. Request notification permission
2. Register Service Worker (/public/sw.js)
3. Subscribe ke push manager
    ↓
POST /push/subscribe
    ↓
PushSubscription table (user_type='mitra', user_id, endpoint, keys)
    ↓
OrderTrackingService::createTracking()
    ↓
PushNotificationService::sendToMitra()
    ↓
WebPush (minishlink/web-push)
    ↓
Browser Push Service (FCM, APNS, Windows Push)
    ↓
Service Worker (@push event listener)
    ↓
showNotification() + actions (open, dismiss)
```

## Setup Steps

### 1. Generate VAPID Keys

VAPID keys diperlukan untuk mengirim push notifications. Pilih salah satu:

#### Option A: Generate via Command (Recommended)
```bash
php artisan web-push:generate-keys
```

#### Option B: Generate Online
Jika command gagal, gunakan tool online:
1. Kunjungi: https://web-push-codelab.glitch.me/
2. Klik "Generate Keys"
3. Copy public key dan private key
4. Set di settings table:

```php
use App\Models\Setting;

Setting::set('vapid_public_key', 'YOUR_PUBLIC_KEY_HERE');
Setting::set('vapid_private_key', 'YOUR_PRIVATE_KEY_HERE');
```

Atau di `.env`:
```env
VAPID_PUBLIC_KEY=your_public_key
VAPID_PRIVATE_KEY=your_private_key
```

### 2. Verify Service Worker

Service worker harus accessible di `/sw.js`:
```bash
curl http://localhost:8000/sw.js | head -5
```

### 3. Test Push Notification

#### Manual Testing di Browser

1. Login sebagai mitra (test account)
2. Buka DevTools → Console
3. Cek apakah ada log "Service Worker registered"
4. Cek apakah ada request ke `/push/subscribe` (Network tab)
5. Browser seharusnya minta permission "Allow notifications"
6. Click "Allow"

#### Create Order untuk Test Push

1. Login sebagai pelanggan di window/tab lain
2. Buat order (jastip, wfh, tenaga, atau service)
3. Assign ke mitra yang sama
4. Cek mitra browser: seharusnya dapat push notification

### 4. Troubleshooting

#### "Push notification not supported"
- Browser tidak support Web Push API
- Hanya modern browsers yang support: Chrome, Firefox, Edge
- Safari doesn't support Web Push yet

#### Service Worker tidak register
```javascript
// Check di console:
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('SW Registrations:', regs);
});
```

#### VAPID keys error
```
Error: No matching VAPID keys
```
Pastikan:
1. Keys sudah disimpan di settings table
2. Keys format benar (base64url encoded)
3. Public key untuk client-side, private key untuk server

#### Notification tidak muncul
```javascript
// Check subscription status:
navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        console.log('Push subscription:', sub);
    });
});
```

#### Expired Subscription
Jika endpoint sudah expired (error 410), subscription otomatis dihapus dari database. Mitra perlu subscribe ulang saat refresh halaman.

## File Structure

```
/app
  /Services
    - PushNotificationService.php (sendToMitra, sendToPelanggan)
  /Http/Controllers
    - PushNotificationController.php (subscribe, unsubscribe, getPublicKey)
  /Console/Commands
    - WebPushGenerateKeys.php
/public
  - sw.js (Service Worker, handle push events)
/resources/views/layouts
  - mitra.blade.php (push subscription script)
/routes
  - web.php (push routes: /push/*)
```

## API Endpoints

### GET /push/public-key
**Public endpoint** - get VAPID public key untuk client-side
```bash
curl http://localhost:8000/push/public-key
```

Response:
```json
{
  "public_key": "BCx2..."
}
```

### POST /push/subscribe
**Authenticated endpoint** (middleware applied via layout)

Subscribe mitra/pelanggan ke push notifications

Request:
```json
{
  "endpoint": "https://fcm.googleapis.com/...",
  "p256dh": "base64url_encoded_key",
  "auth_key": "base64url_encoded_key",
  "user_type": "mitra"
}
```

Response:
```json
{
  "success": true,
  "message": "Push subscription saved"
}
```

### POST /push/unsubscribe
**Authenticated endpoint**

Unsubscribe dari push notifications

Request:
```json
{
  "endpoint": "https://fcm.googleapis.com/...",
  "user_type": "mitra"
}
```

## Integration Points

### OrderTrackingService::createTracking()
Ketika order baru dibuat, push notification dikirim:
```php
$pushService = new PushNotificationService();
$pushService->sendToMitra(
    $mitraId,
    'Order Masuk! 🔔',
    'Ada pesanan baru dari pelanggan. Buka untuk melihat detail.',
    route('mitra.dashboard'),
    'order-masuk'
);
```

### Service Worker (public/sw.js)
```javascript
// Push event
self.addEventListener('push', event => {
    // Parse payload, show notification
});

// Notification click
self.addEventListener('notificationclick', event => {
    // Open URL atau focus existing window
});
```

## Polling Fallback

Push notification adalah suplemen, bukan replacement. Polling tetap jalan:
- Mitra dashboard: polling `/mitra/api/pending-order` setiap 5 detik
- Pelanggan order-tracking: polling `/order-tracking/{id}/status` setiap 5 detik

Jika push gagal/disabled, polling akan mendeteksi order baru.

## Security Considerations

1. **VAPID Keys**: Private key TIDAK boleh exposed di client. Hanya public key yang dikirim ke browser.
2. **Subscription Verification**: Endpoint URL verified - tidak bisa arbitrary URLs.
3. **CSRF Protection**: `/push/subscribe` dilindungi dengan CSRF token
4. **User Isolation**: Mitra hanya bisa subscribe/unsubscribe subscription mereka sendiri

## Browser Support

| Browser | Support | Note |
|---------|---------|------|
| Chrome | ✓ | Full support |
| Firefox | ✓ | Full support |
| Edge | ✓ | Full support |
| Safari | ✗ | Not yet (use polling) |
| Opera | ✓ | Full support |
| Samsung Internet | ✓ | Full support |

## Testing Checklist

- [ ] VAPID keys tersimpan di settings table
- [ ] Service Worker register tanpa error
- [ ] Mitra diminta permission "Allow notifications"
- [ ] Subscription request dikirim ke `/push/subscribe`
- [ ] Subscription tersimpan di `push_subscriptions` table
- [ ] Order baru → push notification muncul
- [ ] Klik notification → buka dashboard/order-tracking
- [ ] Cek logs untuk error messages
- [ ] Test di incognito/private window
- [ ] Test di device lain (jika ada)

## Monitoring

Monitor logs untuk push notification issues:

```bash
# Tail logs
tail -f storage/logs/laravel.log | grep -i push

# Check subscriptions
php artisan tinker
>>> DB::table('push_subscriptions')->count()
>>> DB::table('push_subscriptions')->where('user_type', 'mitra')->get()
```

## Future Improvements

- [ ] Push notification history/archive
- [ ] User preference untuk notification settings
- [ ] Topic-based subscriptions (order_masuk, order_update, promo)
- [ ] Web push analytics (delivered, clicked, dismissed)
- [ ] Batch push notification untuk multiple orders
- [ ] Retry mechanism untuk failed pushes
