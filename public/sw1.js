// (A) INSTANT WORKER ACTIVATION
self.addEventListener("install", evt => self.skipWaiting());

// (B) CLAIM CONTROL INSTANTLY
self.addEventListener("activate", evt => self.clients.claim());

// (C) LISTEN TO PUSH
self.addEventListener("push", evt => {
    const data = evt.data.json();
    console.log("Push", data);
    self.registration.showNotification(data.title, {
        body: data.body,
        data: {
            click_action: 'https://xn--e1aybc.xn--24-6kchemaby3a4d4erbe.xn--p1ai/mobile'
        }
    });
});
