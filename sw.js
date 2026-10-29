// SK Driving School - Service Worker for Clean URL Routing
// Enables /home, /about, /courses, /gallery, /contact on Live Server & static hosts without 404

const CACHE_NAME = 'sk-driving-v4';

const ROUTE_MAP = {
  '/home': '/index.html',
  '/about': '/about.html',
  '/courses': '/courses.html',
  '/gallery': '/gallery.html',
  '/contact': '/contact.html',
  '/product': '/product.html',
  '/cart': '/cart.html',
  '/checkout': '/checkout.html'
};

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.mode === 'navigate') {
    const url = new URL(event.request.url);
    if (url.origin === self.location.origin) {
      let path = url.pathname;
      
      // Remove trailing slash if present
      if (path.length > 1 && path.endsWith('/')) {
        path = path.slice(0, -1);
      }

      // Check for clean route match
      if (ROUTE_MAP[path]) {
        event.respondWith(fetch(ROUTE_MAP[path]));
        return;
      }

      // Check if path is in subfolder, e.g. /something/about
      const segments = path.split('/').filter(Boolean);
      const lastSegment = segments.length ? '/' + segments[segments.length - 1] : '/';
      if (ROUTE_MAP[lastSegment]) {
        const resolvedPath = path.substring(0, path.lastIndexOf('/')) + ROUTE_MAP[lastSegment];
        event.respondWith(fetch(resolvedPath));
        return;
      }
    }
  }
});
