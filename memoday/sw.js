const CACHE_NAME = 'memoday-v1';
const urlsToCache = [
    '/fs/memoday/',
    '/fs/memoday/index.html',
    '/fs/memoday/edit.html',
    '/fs/memoday/record.html',
    '/fs/memoday/manifest.json',
    '/fs/memoday/icon.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
});
