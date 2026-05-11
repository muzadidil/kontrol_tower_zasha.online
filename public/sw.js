self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Zasha';
    const options = {
        body: data.body || 'Ada notifikasi baru',
        icon: '/img/logo-192.png',
        badge: '/img/badge-72.png',
        vibrate: [300, 100, 300, 100, 300],
        tag: data.tag || 'zasha-notification',
        requireInteraction: data.requireInteraction || false,
        data: {
            url: data.url || '/mitra/dashboard',
            tracking_id: data.tracking_id || null
        },
        actions: [
            { action: 'open', title: 'Buka' },
            { action: 'dismiss', title: 'Tutup' }
        ]
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'dismiss' || !event.notification.data.url) {
        return;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Cek apakah ada window yang sudah membuka URL ini
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === event.notification.data.url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Kalau tidak ada, buka window baru
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});

self.addEventListener('notificationclose', function(event) {
    console.log('Notification dismissed:', event.notification.tag);
});
