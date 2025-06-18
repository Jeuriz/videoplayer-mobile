/**
 * Service Worker for VideoPlayer Mobile Theme
 * Provides offline functionality and caching
 * 
 * @package VideoPlayerMobile
 * @version 1.0.0
 */

const CACHE_NAME = 'videoplayer-mobile-v1.0.0';
const DYNAMIC_CACHE = 'videoplayer-dynamic-v1.0.0';

// Files to cache immediately
const STATIC_ASSETS = [
    '/',
    '/wp-content/themes/videoplayer-mobile/style.css',
    '/wp-content/themes/videoplayer-mobile/js/main.js',
    '/wp-includes/js/jquery/jquery.min.js',
    // Add other critical assets
];

// Runtime caching strategies
const CACHE_STRATEGIES = {
    // Images - Cache First strategy
    images: {
        urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp)$/,
        strategy: 'CacheFirst',
        cacheName: 'videoplayer-images',
        maxEntries: 100,
        maxAgeSeconds: 30 * 24 * 60 * 60 // 30 days
    },
    
    // CSS and JS - Stale While Revalidate
    assets: {
        urlPattern: /\.(?:css|js)$/,
        strategy: 'StaleWhileRevalidate',
        cacheName: 'videoplayer-assets',
        maxEntries: 50,
        maxAgeSeconds: 7 * 24 * 60 * 60 // 7 days
    },
    
    // API calls - Network First
    api: {
        urlPattern: /\/wp-json\//,
        strategy: 'NetworkFirst',
        cacheName: 'videoplayer-api',
        maxEntries: 50,
        maxAgeSeconds: 5 * 60 // 5 minutes
    },
    
    // Pages - Network First with fallback
    pages: {
        urlPattern: /\/(?!wp-admin|wp-login)/,
        strategy: 'NetworkFirst',
        cacheName: 'videoplayer-pages',
        maxEntries: 30,
        maxAgeSeconds: 24 * 60 * 60 // 1 day
    }
};

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Service Worker: Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Service Worker: Static assets cached');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Service Worker: Cache failed:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && 
                            cacheName !== DYNAMIC_CACHE &&
                            !cacheName.startsWith('videoplayer-')) {
                            console.log('Service Worker: Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip Chrome extension requests
    if (url.protocol === 'chrome-extension:') {
        return;
    }
    
    // Skip admin and login pages
    if (url.pathname.includes('/wp-admin/') || 
        url.pathname.includes('/wp-login.php')) {
        return;
    }
    
    event.respondWith(handleRequest(request));
});

// Handle different types of requests
async function handleRequest(request) {
    const url = new URL(request.url);
    
    try {
        // Images strategy
        if (CACHE_STRATEGIES.images.urlPattern.test(url.pathname)) {
            return await cacheFirstStrategy(request, CACHE_STRATEGIES.images);
        }
        
        // CSS/JS assets strategy
        if (CACHE_STRATEGIES.assets.urlPattern.test(url.pathname)) {
            return await staleWhileRevalidateStrategy(request, CACHE_STRATEGIES.assets);
        }
        
        // API calls strategy
        if (CACHE_STRATEGIES.api.urlPattern.test(url.pathname)) {
            return await networkFirstStrategy(request, CACHE_STRATEGIES.api);
        }
        
        // Video files - always try network first
        if (url.pathname.includes('/videos/') || url.pathname.match(/\.(mp4|webm|ogg)$/)) {
            return await fetch(request);
        }
        
        // Pages strategy
        if (CACHE_STRATEGIES.pages.urlPattern.test(url.pathname)) {
            return await networkFirstStrategy(request, CACHE_STRATEGIES.pages);
        }
        
        // Default: try cache first, then network
        return await cacheFirstStrategy(request, {
            cacheName: DYNAMIC_CACHE,
            maxEntries: 100,
            maxAgeSeconds: 24 * 60 * 60
        });
        
    } catch (error) {
        console.error('Service Worker: Request failed:', error);
        return await getOfflineFallback(request);
    }
}

// Cache First Strategy
async function cacheFirstStrategy(request, config) {
    const cache = await caches.open(config.cacheName);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
        // Check if cache is expired
        const dateHeader = cachedResponse.headers.get('date');
        const cachedDate = new Date(dateHeader).getTime();
        const now = Date.now();
        
        if (config.maxAgeSeconds && (now - cachedDate) > (config.maxAgeSeconds * 1000)) {
            // Cache expired, fetch new version
            try {
                const networkResponse = await fetch(request);
                if (networkResponse.ok) {
                    await cache.put(request, networkResponse.clone());
                    await cleanupCache(cache, config.maxEntries);
                    return networkResponse;
                }
            } catch (error) {
                console.warn('Service Worker: Network failed, using stale cache');
            }
        }
        
        return cachedResponse;
    }
    
    // Not in cache, fetch from network
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            await cache.put(request, networkResponse.clone());
            await cleanupCache(cache, config.maxEntries);
        }
        return networkResponse;
    } catch (error) {
        throw error;
    }
}

// Network First Strategy
async function networkFirstStrategy(request, config) {
    const cache = await caches.open(config.cacheName);
    
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            await cache.put(request, networkResponse.clone());
            await cleanupCache(cache, config.maxEntries);
        }
        return networkResponse;
    } catch (error) {
        console.warn('Service Worker: Network failed, trying cache');
        const cachedResponse = await cache.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        throw error;
    }
}

// Stale While Revalidate Strategy
async function staleWhileRevalidateStrategy(request, config) {
    const cache = await caches.open(config.cacheName);
    const cachedResponse = await cache.match(request);
    
    // Start network request (don't await)
    const networkResponsePromise = fetch(request)
        .then(async (networkResponse) => {
            if (networkResponse.ok) {
                await cache.put(request, networkResponse.clone());
                await cleanupCache(cache, config.maxEntries);
            }
            return networkResponse;
        })
        .catch((error) => {
            console.warn('Service Worker: Background fetch failed:', error);
        });
    
    // Return cached version immediately if available
    if (cachedResponse) {
        return cachedResponse;
    }
    
    // Wait for network if no cache
    return await networkResponsePromise;
}

// Clean up cache to respect maxEntries
async function cleanupCache(cache, maxEntries) {
    if (!maxEntries) return;
    
    const keys = await cache.keys();
    if (keys.length > maxEntries) {
        // Remove oldest entries
        const entriesToDelete = keys.length - maxEntries;
        for (let i = 0; i < entriesToDelete; i++) {
            await cache.delete(keys[i]);
        }
    }
}

// Offline fallback
async function getOfflineFallback(request) {
    const url = new URL(request.url);
    
    // For pages, try to return a cached page or offline page
    if (request.destination === 'document') {
        // Try to find any cached page
        const cache = await caches.open(CACHE_NAME);
        const keys = await cache.keys();
        
        for (const key of keys) {
            if (new URL(key.url).pathname === '/') {
                const cachedResponse = await cache.match(key);
                if (cachedResponse) {
                    return cachedResponse;
                }
            }
        }
        
        // Return basic offline page
        return new Response(getOfflineHTML(), {
            headers: { 'Content-Type': 'text/html' }
        });
    }
    
    // For images, return placeholder
    if (request.destination === 'image') {
        return new Response(getImagePlaceholder(), {
            headers: { 'Content-Type': 'image/svg+xml' }
        });
    }
    
    // For other resources, return network error
    return new Response('Network error', {
        status: 408,
        headers: { 'Content-Type': 'text/plain' }
    });
}

// Basic offline HTML
function getOfflineHTML() {
    return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sin conexión - VideoPlayer</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
                background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
                color: #ffffff;
                margin: 0;
                padding: 20px;
                text-align: center;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            }
            .offline-icon {
                font-size: 4rem;
                margin-bottom: 20px;
            }
            h1 {
                color: #ff6b6b;
                margin-bottom: 15px;
            }
            p {
                color: #aaa;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .retry-btn {
                background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="offline-icon">📡</div>
        <h1>Sin conexión a Internet</h1>
        <p>Parece que no tienes conexión a Internet. Revisa tu conexión e intenta de nuevo.</p>
        <button class="retry-btn" onclick="window.location.reload()">
            Reintentar
        </button>
    </body>
    </html>
    `;
}

// Image placeholder SVG
function getImagePlaceholder() {
    return `
    <svg width="320" height="180" viewBox="0 0 320 180" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="320" height="180" fill="#333"/>
        <text x="160" y="90" fill="#666" text-anchor="middle" dominant-baseline="central" font-family="sans-serif" font-size="14">
            Imagen no disponible
        </text>
    </svg>
    `;
}

// Background sync for analytics and offline actions
self.addEventListener('sync', (event) => {
    console.log('Service Worker: Background sync triggered:', event.tag);
    
    if (event.tag === 'video-analytics') {
        event.waitUntil(syncVideoAnalytics());
    }
    
    if (event.tag === 'offline-comments') {
        event.waitUntil(syncOfflineComments());
    }
});

// Sync video analytics when online
async function syncVideoAnalytics() {
    try {
        // Get stored analytics data from IndexedDB
        const analyticsData = await getStoredAnalytics();
        
        if (analyticsData && analyticsData.length > 0) {
            for (const data of analyticsData) {
                await fetch('/wp-json/videoplayer/v1/analytics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
            }
            
            // Clear stored data after successful sync
            await clearStoredAnalytics();
            console.log('Service Worker: Analytics synced successfully');
        }
    } catch (error) {
        console.error('Service Worker: Analytics sync failed:', error);
    }
}

// Sync offline comments
async function syncOfflineComments() {
    try {
        const offlineComments = await getStoredComments();
        
        if (offlineComments && offlineComments.length > 0) {
            for (const comment of offlineComments) {
                const formData = new FormData();
                Object.keys(comment).forEach(key => {
                    formData.append(key, comment[key]);
                });
                
                await fetch('/wp-comments-post.php', {
                    method: 'POST',
                    body: formData
                });
            }
            
            await clearStoredComments();
            console.log('Service Worker: Offline comments synced successfully');
        }
    } catch (error) {
        console.error('Service Worker: Comment sync failed:', error);
    }
}

// Push notifications
self.addEventListener('push', (event) => {
    if (!event.data) return;
    
    const data = event.data.json();
    const options = {
        body: data.body || 'Nuevo contenido disponible',
        icon: '/wp-content/themes/videoplayer-mobile/images/icon-192x192.png',
        badge: '/wp-content/themes/videoplayer-mobile/images/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/'
        },
        actions: [
            {
                action: 'view',
                title: 'Ver ahora',
                icon: '/wp-content/themes/videoplayer-mobile/images/play-icon.png'
            },
            {
                action: 'dismiss',
                title: 'Cerrar',
                icon: '/wp-content/themes/videoplayer-mobile/images/close-icon.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'VideoPlayer', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'view') {
        const url = event.notification.data.url || '/';
        event.waitUntil(
            clients.openWindow(url)
        );
    }
});

// Placeholder functions for IndexedDB operations
async function getStoredAnalytics() {
    // Implement IndexedDB read operation
    return [];
}

async function clearStoredAnalytics() {
    // Implement IndexedDB clear operation
}

async function getStoredComments() {
    // Implement IndexedDB read operation
    return [];
}

async function clearStoredComments() {
    // Implement IndexedDB clear operation
}

// Message handling for communication with main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({
            type: 'VERSION',
            version: CACHE_NAME
        });
    }
});

console.log('Service Worker: Loaded successfully');