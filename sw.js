const CACHE_NAME = 'tenezer-cache-v2';

const PRECACHE_URLS = [
  './',
  'index.php',
  'offline.html',
  'manifest.json',
  'includes/logo.jpeg',
  'includes/icon-72.png',
  'includes/icon-96.png',
  'includes/icon-128.png',
  'includes/icon-144.png',
  'includes/icon-152.png',
  'includes/icon-192.png',
  'includes/icon-384.png',
  'includes/icon-512.png',
  'assets/js/request-form.js',
  'assets/js/pwa-app.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
    )).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;

  if (request.method !== 'GET') {
    return; // Ne pas intercepter les POST (formulaires, process_request.php, etc.)
  }

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) {
    return; // Laisser passer les ressources externes (CDN) sans les gérer
  }

  // Navigation (pages PHP) : réseau en priorité, secours cache offline si hors-ligne
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('offline.html'))
    );
    return;
  }

  // Fichiers statiques (assets/, includes/*.png|jpeg) : cache en priorité, réseau en secours
  if (url.pathname.includes('/assets/') || /\.(png|jpe?g|webp|svg|ico)$/i.test(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          }
          return response;
        });
      })
    );
  }
});
