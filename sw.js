// Campus Connect - Service Worker
// This file handles offline caching and push notifications for PWA functionality

const CACHE_NAME = 'campus-connect-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/dashboard.html',
  '/schedule.html',
  '/booking.html',
  '/report.html',
  '/admin.html',
  '/contact.html',
  '/style.css',
  '/script.js',
  '/manifest.json'
];

// Install event - cache files
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Service Worker: Caching files');
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        console.log('Service Worker: Installed successfully');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.log('Service Worker: Cache failed', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('Service Worker: Deleting old cache', cache);
            return caches.delete(cache);
          }
        })
      );
    })
    .then(() => {
      console.log('Service Worker: Activated successfully');
      return self.clients.claim();
    })
  );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', (event) => {
  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Return cached version or fetch from network
        if (response) {
          console.log('Service Worker: Serving from cache', event.request.url);
          return response;
        }
        
        console.log('Service Worker: Fetching from network', event.request.url);
        return fetch(event.request)
          .then((response) => {
            // Check if valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            // Cache the fetched response
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              });

            return response;
          })
          .catch((error) => {
            console.log('Service Worker: Fetch failed', error);
            // Return offline page if available
            return caches.match('/index.html');
          });
      })
  );
});

// Push notification event
self.addEventListener('push', (event) => {
  console.log('Service Worker: Push notification received');
  
  let notificationData = {};
  
  if (event.data) {
    notificationData = event.data.json();
  } else {
    notificationData = {
      title: 'Campus Connect',
      body: 'You have a new notification',
      icon: 'icon-192x192.png',
      badge: 'icon-192x192.png'
    };
  }

  const options = {
    body: notificationData.body,
    icon: notificationData.icon || 'icon-192x192.png',
    badge: notificationData.badge || 'icon-192x192.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'View Details',
        icon: 'icon-192x192.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: 'icon-192x192.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(notificationData.title, options)
  );
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
  console.log('Service Worker: Notification clicked');
  
  event.notification.close();

  if (event.action === 'explore') {
    // Open the app when user clicks on notification
    event.waitUntil(
      clients.openWindow('/dashboard.html')
    );
  } else if (event.action === 'close') {
    // Just close the notification
    event.notification.close();
  } else {
    // Default action - open the app
    event.waitUntil(
      clients.openWindow('/dashboard.html')
    );
  }
});

// Background sync event (for offline booking/reporting)
self.addEventListener('sync', (event) => {
  console.log('Service Worker: Background sync triggered');
  
  if (event.tag === 'sync-bookings') {
    event.waitUntil(syncBookings());
  } else if (event.tag === 'sync-reports') {
    event.waitUntil(syncReports());
  }
});

// Sync bookings function
function syncBookings() {
  // Get pending bookings from IndexedDB and sync with server
  console.log('Service Worker: Syncing bookings...');
  return Promise.resolve();
}

// Sync reports function
function syncReports() {
  // Get pending reports from IndexedDB and sync with server
  console.log('Service Worker: Syncing reports...');
  return Promise.resolve();
}

// Message event - communicate with client
self.addEventListener('message', (event) => {
  console.log('Service Worker: Message received', event.data);
  
  if (event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
});
