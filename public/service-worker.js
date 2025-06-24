const CACHE_NAME = 'pwa-cache-v1';
const urlsToCache = [
  '/',
  '/pwa/offline.html',
  '/css/app.css',
  '/js/app.js',
  '/images/logo.png',
  '/favicon.ico'
];

let CACHE_IGNORED = false;

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'CACHE_STATUS') {
    CACHE_IGNORED = event.data.isCacheIgnored;
  }

  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            return caches.delete(cacheName);
          })
        );
      }).then(() => {
        // Notify the main thread that cache has been cleared
        self.clients.matchAll().then(clients => {
          clients.forEach(client => {
            client.postMessage({
              type: 'CACHE_CLEARED',
              success: true
            });
          });
        });
      }).catch(error => {
        // Notify the main thread about the error
        self.clients.matchAll().then(clients => {
          clients.forEach(client => {
            client.postMessage({
              type: 'CACHE_CLEARED',
              success: false,
              error: error.message
            });
          });
        });
      })
    );
  }
});

function isCacheableUrl(url) {
  const supportedSchemes = ['http:', 'https:'];
  try {
    let urlObj;

    if (url instanceof Request) {
      urlObj = new URL(url.url);
      return supportedSchemes.includes(urlObj.protocol) && !isAdminUrl(urlObj);
    }

    if (typeof url === 'string') {
      urlObj = new URL(url);
      return supportedSchemes.includes(urlObj.protocol) && !isAdminUrl(urlObj);
    }

    if (url instanceof URL) {
      return supportedSchemes.includes(url.protocol) && !isAdminUrl(url);
    }

    return false;
  } catch (e) {
    return false;
  }
}

function isAdminUrl(url) {
  if (CACHE_IGNORED) {
    return true;
  }

  return url.pathname.includes('/api/') ||
         url.pathname.includes('/auth/') ||
         url.pathname.includes('/ajax/') ||
         url.pathname.includes('/admin-cp/') ||
         url.pathname.includes('/dashboard/');
}

self.addEventListener('install', event => {
  event.waitUntil(
    Promise.all([
      caches.open(CACHE_NAME)
        .then(cache => {
          const cacheableUrls = urlsToCache.filter(url => isCacheableUrl(url));
          return cache.addAll(cacheableUrls);
        }),
      self.skipWaiting()
    ])
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    Promise.all([
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.filter(cacheName => {
            return cacheName !== CACHE_NAME;
          }).map(cacheName => {
            return caches.delete(cacheName);
          })
        );
      }),
      self.clients.claim()
    ])
  );
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  if (url.pathname.includes('/api/') ||
      url.pathname.includes('/auth/') ||
      url.pathname.includes('/ajax/') ||
      url.pathname.includes('/admin-cp/') ||
      url.pathname.includes('/dashboard/') ||
      url.search.includes('token=') ||
      url.search.includes('key=') ||
      url.search.includes('password=') ||
      url.search.includes('email=')) {
    return;
  }

  if (!isCacheableUrl(event.request)) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }

        const fetchRequest = event.request.clone();

        return fetch(fetchRequest)
          .then(response => {
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            const responseToCache = response.clone();

            const cacheUrl = new URL(event.request.url);
            const shouldCache = !(
              cacheUrl.pathname.includes('/api/') ||
              cacheUrl.pathname.includes('/auth/') ||
              cacheUrl.pathname.includes('/ajax/') ||
              cacheUrl.pathname.includes('/admin-cp/') ||
              cacheUrl.pathname.includes('/dashboard/') ||
              cacheUrl.search.includes('token=') ||
              cacheUrl.search.includes('key=') ||
              cacheUrl.search.includes('password=') ||
              cacheUrl.search.includes('email=')
            );

            if (shouldCache) {
              caches.open(CACHE_NAME)
                .then(cache => {
                  try {
                    cache.put(event.request, responseToCache);
                  } catch (error) {
                    console.error('Failed to cache response:', error);
                  }
                });
            }

            return response;
          })
          .catch(() => {
            if (event.request.mode === 'navigate') {
              return caches.match('/pwa/offline.html')
                .then(offlineResponse => {
                  return offlineResponse || new Response('You are offline. Please check your internet connection.', {
                    headers: { 'Content-Type': 'text/html' }
                  });
                });
            }

            if (event.request.destination === 'image') {
              return caches.match('/images/offline-image.png')
                .catch(() => new Response('Image not available offline', { status: 503 }));
            }

            return new Response('Resource not available offline', { status: 503 });
          });
      })
  );
});
