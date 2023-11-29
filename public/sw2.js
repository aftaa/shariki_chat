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

