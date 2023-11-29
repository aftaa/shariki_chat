// (A) INSTANT WORKER ACTIVATION
self.addEventListener("install", evt => self.skipWaiting());

// (B) CLAIM CONTROL INSTANTLY
self.addEventListener("activate", evt => self.clients.claim());

// (C) LISTEN TO PUSH
self.addEventListener("push", evt => {
    const data = evt.data.json();
    console.log("Push", data);
    return self.registration.showNotification('Chat', {
        body: 'New message',
        data: {
            url: 'https://xn--80a0bn.xn--24-6kchemaby3a4d4erbe.xn--p1ai/chat'
        },
        click_action: "https://xn--80a0bn.xn--24-6kchemaby3a4d4erbe.xn--p1ai/chat"
        actions: [{action: "open_url", title: "Чат"}]
    });
});

self.addEventListener('notificationclick', function(event) {
    const target = event.notification.data.click_action || '/';
    event.notification.close();

    // этот код должен проверять список открытых вкладок и переключатся на открытую
    // вкладку с ссылкой если такая есть, иначе открывает новую вкладку
    event.waitUntil(clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    }).then(function(clientList) {
        // clientList почему-то всегда пуст!?
        for (var i = 0; i < clientList.length; i++) {
            var client = clientList[i];
            if (client.url === target && 'focus' in client) {
                return client.focus();
            }
        }

        // Открываем новое окно
        return clients.openWindow(target);
    }));
});
