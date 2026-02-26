const CACHE_NAME = "school-library-v1";
const filesToCache = [
    '/',
    '/offline.html',
    '/logo.png',
    '/hero.png'
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(filesToCache);
        })
    );
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener("fetch", (event) => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    if (event.request.url.startsWith('http')) {
                        cache.put(event.request, responseClone);
                    }
                });
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((response) => {
                    return response || caches.match('/offline.html');
                });
            })
    );
});
