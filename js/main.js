/**
 * Main JavaScript file for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Theme configuration
    const VideoPlayerTheme = {
        config: {
            isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
            isTouch: 'ontouchstart' in window || navigator.maxTouchPoints > 0,
            animations: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            },
            breakpoints: {
                mobile: 480,
                tablet: 768,
                desktop: 1024,
                large: 1200
            }
        },

        // Initialize theme
        init: function() {
            this.setupEventListeners();
            this.initMobileMenu();
            this.initVideoPlayer();
            this.initInfiniteScroll();
            this.initLazyLoading();
            this.initAnalytics();
            this.initPerformanceOptimizations();
            this.initAccessibility();
            this.initPWA();
            
            // Trigger custom event
            this.triggerEvent('themeReady');
        },

        // Event listeners setup
        setupEventListeners: function() {
            // DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
            } else {
                this.onDOMReady();
            }

            // Window events
            window.addEventListener('load', () => this.onWindowLoad());
            window.addEventListener('resize', this.debounce(() => this.onWindowResize(), 250));
            window.addEventListener('scroll', this.throttle(() => this.onWindowScroll(), 16));
            window.addEventListener('orientationchange', () => this.onOrientationChange());

            // Visibility change (for pause/resume functionality)
            document.addEventListener('visibilitychange', () => this.onVisibilityChange());

            // Online/offline events
            window.addEventListener('online', () => this.onOnline());
            window.addEventListener('offline', () => this.onOffline());
        },

        // DOM ready handler
        onDOMReady: function() {
            this.setupVideoCards();
            this.setupSearchEnhancements();
            this.setupFormValidation();
            this.setupTooltips();
            this.setupModalSystem();
            this.setupNotificationSystem();
            this.initThemeToggle();
        },

        // Window load handler
        onWindowLoad: function() {
            this.hideLoadingScreen();
            this.initAnimations();
            this.preloadCriticalResources();
            this.setupServiceWorker();
        },

        // Window resize handler
        onWindowResize: function() {
            this.updateViewportUnits();
            this.adjustMobileMenu();
            this.recalculateVideoSizes();
        },

        // Window scroll handler
        onWindowScroll: function() {
            this.updateScrollProgress();
            this.handleStickyElements();
            this.updateVisibleVideos();
            this.handleBackToTop();
        },

        // Mobile menu functionality
        initMobileMenu: function() {
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileNav = document.querySelector('.mobile-navigation');
            const menuClose = document.querySelector('.mobile-nav-close');
            const overlay = this.createOverlay();

            if (!menuToggle || !mobileNav) return;

            const openMenu = () => {
                mobileNav.classList.add('active');
                document.body.classList.add('menu-open');
                overlay.classList.add('active');
                menuToggle.setAttribute('aria-expanded', 'true');
                this.trapFocus(mobileNav);
            };

            const closeMenu = () => {
                mobileNav.classList.remove('active');
                document.body.classList.remove('menu-open');
                overlay.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                this.releaseFocus();
                menuToggle.focus();
            };

            // Event listeners
            menuToggle.addEventListener('click', openMenu);
            if (menuClose) menuClose.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                    closeMenu();
                }
            });

            // Swipe to close on mobile
            if (this.config.isTouch) {
                this.setupSwipeGestures(mobileNav, closeMenu);
            }
        },

        // Video player enhancements
        initVideoPlayer: function() {
            const videoPlayers = document.querySelectorAll('.video-player');
            
            videoPlayers.forEach(player => {
                this.enhanceVideoPlayer(player);
            });

            // Global video events
            document.addEventListener('click', (e) => {
                if (e.target.closest('.video-overlay')) {
                    this.handleVideoClick(e);
                }
            });
        },

        // Enhanced video player
        enhanceVideoPlayer: function(player) {
            const video = player.querySelector('video');
            const overlay = player.querySelector('.video-overlay');
            const controls = player.querySelector('.video-controls');

            if (!video) return;

            // Custom controls
            this.setupVideoControls(player, video);

            // Picture-in-picture support
            if (document.pictureInPictureEnabled) {
                this.addPiPButton(player, video);
            }

            // Fullscreen support
            this.setupFullscreenToggle(player, video);

            // Video analytics
            this.trackVideoEvents(video);

            // Touch gestures for mobile
            if (this.config.isTouch) {
                this.setupVideoTouchGestures(player, video);
            }

            // Keyboard shortcuts
            this.setupVideoKeyboardShortcuts(player, video);
        },

        // Setup video controls
        setupVideoControls: function(player, video) {
            const progressBar = player.querySelector('.progress-bar');
            const progressFill = player.querySelector('.progress-fill');
            const timeDisplay = player.querySelector('.time-display');
            const playButton = player.querySelector('.control-btn[onclick*="togglePlay"]');

            if (!progressBar || !progressFill) return;

            // Progress tracking
            video.addEventListener('timeupdate', () => {
                const progress = (video.currentTime / video.duration) * 100;
                progressFill.style.width = progress + '%';
                
                if (timeDisplay) {
                    const current = this.formatTime(video.currentTime);
                    const duration = this.formatTime(video.duration);
                    timeDisplay.textContent = `${current} / ${duration}`;
                }
            });

            // Seeking
            progressBar.addEventListener('click', (e) => {
                const rect = progressBar.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const percentage = clickX / rect.width;
                video.currentTime = percentage * video.duration;
            });

            // Play/pause toggle
            if (playButton) {
                playButton.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                        playButton.innerHTML = '⏸️';
                    } else {
                        video.pause();
                        playButton.innerHTML = '▶️';
                    }
                });
            }

            // Volume control
            this.setupVolumeControl(player, video);
        },

        // Video click handler with redirect logic
        handleVideoClick: function(e) {
            e.preventDefault();
            
            const videoPlayer = e.target.closest('.video-player');
            const videoContainer = e.target.closest('.video-container');
            
            if (!videoPlayer || !window.videoPlayerConfig) return;

            const { redirectUrl, maxRedirects, redirectEnabled } = window.videoPlayerConfig;
            
            if (!redirectEnabled) {
                this.playVideo(videoPlayer);
                return;
            }

            // Check click count and session
            if (!sessionStorage.getItem('sessionStarted')) {
                localStorage.removeItem('clickCount');
                sessionStorage.setItem('sessionStarted', 'true');
            }

            let clickCount = parseInt(localStorage.getItem('clickCount')) || 0;
            clickCount++;
            localStorage.setItem('clickCount', clickCount);

            if (clickCount <= maxRedirects) {
                // Show loading indicator
                this.showRedirectLoader();
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 500);
            } else {
                // Allow normal video play
                this.playVideo(videoPlayer);
            }
        },

        // Play video function
        playVideo: function(player) {
            const video = player.querySelector('video');
            const overlay = player.querySelector('.video-overlay');

            if (video) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 300);
                
                video.play().then(() => {
                    this.trackEvent('video_play', {
                        video_title: this.getVideoTitle(player),
                        video_id: this.getVideoId(player)
                    });
                }).catch(console.error);
            }
        },

        // Infinite scroll for video grids
        initInfiniteScroll: function() {
            const loadMoreBtn = document.getElementById('load-more-btn');
            const videosContainer = document.getElementById('videos-container');
            
            if (!loadMoreBtn || !videosContainer) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !loadMoreBtn.disabled) {
                        this.loadMoreVideos(loadMoreBtn, videosContainer);
                    }
                });
            }, { rootMargin: '100px' });

            observer.observe(loadMoreBtn);
        },

        // Load more videos
        loadMoreVideos: function(button, container) {
            const currentPage = parseInt(button.dataset.page);
            const maxPages = parseInt(button.dataset.max);
            const nextPage = currentPage + 1;

            if (nextPage > maxPages) {
                button.style.display = 'none';
                return;
            }

            // Show loading state
            button.disabled = true;
            button.querySelector('.btn-text').style.display = 'none';
            button.querySelector('.btn-loading').style.display = 'inline';

            // Build AJAX URL
            const url = new URL(window.location);
            url.searchParams.set('paged', nextPage);

            fetch(url.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newVideos = doc.querySelectorAll('.video-card');

                    if (newVideos.length > 0) {
                        newVideos.forEach((video, index) => {
                            video.style.animationDelay = (index * 0.1) + 's';
                            container.appendChild(video);
                        });

                        button.dataset.page = nextPage;
                        
                        // Setup new video cards
                        this.setupVideoCards(newVideos);
                        
                        if (nextPage >= maxPages) {
                            button.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more videos:', error);
                    this.showNotification('Error al cargar más videos', 'error');
                })
                .finally(() => {
                    // Hide loading state
                    button.disabled = false;
                    button.querySelector('.btn-text').style.display = 'inline';
                    button.querySelector('.btn-loading').style.display = 'none';
                });
        },

        // Lazy loading implementation
        initLazyLoading: function() {
            const images = document.querySelectorAll('img[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            this.loadImage(img);
                            imageObserver.unobserve(img);
                        }
                    });
                }, { rootMargin: '50px' });

                images.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for older browsers
                images.forEach(img => this.loadImage(img));
            }
        },

        // Load image with fade-in effect
        loadImage: function(img) {
            const src = img.dataset.src;
            if (!src) return;

            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';

            img.onload = () => {
                img.style.opacity = '1';
                img.classList.add('loaded');
            };

            img.onerror = () => {
                img.classList.add('error');
                // Set fallback image
                img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDMyMCAxODAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMjAiIGhlaWdodD0iMTgwIiBmaWxsPSIjMzMzIi8+Cjx0ZXh0IHg9IjE2MCIgeT0iOTAiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJjZW50cmFsIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCI+SW1hZ2VuIG5vIGRpc3BvbmlibGU8L3RleHQ+Cjwvc3ZnPg==';
            };

            img.src = src;
            img.removeAttribute('data-src');
        },

        // Setup video cards with interactions
        setupVideoCards: function(cards = null) {
            const videoCards = cards || document.querySelectorAll('.video-card');
            
            videoCards.forEach(card => {
                // Hover effects
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });

                // Click analytics
                card.addEventListener('click', () => {
                    this.trackVideoCardClick(card);
                });

                // Intersection observer for view tracking
                this.observeVideoCard(card);
            });
        },

        // Track video card impressions
        observeVideoCard: function(card) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const videoTitle = this.getVideoTitle(card);
                        const videoId = this.getVideoId(card);
                        
                        this.trackEvent('video_impression', {
                            video_title: videoTitle,
                            video_id: videoId,
                            position: Array.from(card.parentNode.children).indexOf(card)
                        });
                        
                        observer.unobserve(card);
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(card);
        },

        // Analytics and tracking
        initAnalytics: function() {
            // Track page views
            this.trackPageView();
            
            // Track user engagement
            this.trackUserEngagement();
            
            // Track scroll depth
            this.trackScrollDepth();
            
            // Track search queries
            this.trackSearchQueries();
        },

        // Track events
        trackEvent: function(eventName, parameters = {}) {
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, parameters);
            }
            
            // Custom analytics
            if (window.customAnalytics) {
                window.customAnalytics.track(eventName, parameters);
            }
            
            // Console log for debugging
            if (this.config.debug) {
                console.log('Event tracked:', eventName, parameters);
            }
        },

        // Performance optimizations
        initPerformanceOptimizations: function() {
            // Preload critical resources
            this.preloadCriticalResources();
            
            // Optimize images
            this.optimizeImages();
            
            // Defer non-critical scripts
            this.deferNonCriticalScripts();
            
            // Cache management
            this.initCacheManagement();
        },

        // Accessibility features
        initAccessibility: function() {
            // Skip to content link
            this.addSkipToContentLink();
            
            // Keyboard navigation
            this.enhanceKeyboardNavigation();
            
            // ARIA labels and live regions
            this.setupARIALabels();
            
            // Focus management
            this.setupFocusManagement();
            
            // Reduced motion support
            this.handleReducedMotion();
        },

        // PWA functionality
        initPWA: function() {
            // Service worker registration
            this.setupServiceWorker();
            
            // Install prompt
            this.setupInstallPrompt();
            
            // Offline functionality
            this.setupOfflineSupport();
            
            // Background sync
            this.setupBackgroundSync();
        },

        // Notification system
        showNotification: function(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                    <span class="notification-message">${message}</span>
                    <button class="notification-close" aria-label="Cerrar">&times;</button>
                </div>
            `;

            // Styles
            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                background: this.getNotificationColor(type),
                color: 'white',
                padding: '15px 20px',
                borderRadius: '8px',
                boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
                zIndex: '10000',
                maxWidth: '350px',
                animation: 'slideInRight 0.3s ease-out'
            });

            document.body.appendChild(notification);

            // Close functionality
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => this.hideNotification(notification));

            // Auto hide
            if (duration > 0) {
                setTimeout(() => this.hideNotification(notification), duration);
            }

            return notification;
        },

        hideNotification: function(notification) {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        },

        // Utility functions
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        formatTime: function(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        getVideoTitle: function(element) {
            const titleElement = element.querySelector('.video-title, .video-card-title');
            return titleElement ? titleElement.textContent.trim() : '';
        },

        getVideoId: function(element) {
            return element.id || element.dataset.videoId || '';
        },

        getNotificationIcon: function(type) {
            const icons = {
                success: '✅',
                error: '❌', 
                warning: '⚠️',
                info: 'ℹ️'
            };
            return icons[type] || icons.info;
        },

        getNotificationColor: function(type) {
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            return colors[type] || colors.info;
        },

        createOverlay: function() {
            let overlay = document.querySelector('.theme-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'theme-overlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                `;
                document.body.appendChild(overlay);
            }
            return overlay;
        },

        triggerEvent: function(eventName, detail = {}) {
            const event = new CustomEvent(eventName, { detail });
            document.dispatchEvent(event);
        },

        // Additional stub methods (implement as needed)
        onOrientationChange: function() {},
        onVisibilityChange: function() {},
        onOnline: function() {},
        onOffline: function() {},
        hideLoadingScreen: function() {},
        initAnimations: function() {},
        preloadCriticalResources: function() {},
        setupServiceWorker: function() {},
        updateViewportUnits: function() {},
        adjustMobileMenu: function() {},
        recalculateVideoSizes: function() {},
        updateScrollProgress: function() {},
        handleStickyElements: function() {},
        updateVisibleVideos: function() {},
        handleBackToTop: function() {},
        setupSwipeGestures: function() {},
        addPiPButton: function() {},
        setupFullscreenToggle: function() {},
        trackVideoEvents: function() {},
        setupVideoTouchGestures: function() {},
        setupVideoKeyboardShortcuts: function() {},
        setupVolumeControl: function() {},
        showRedirectLoader: function() {},
        setupSearchEnhancements: function() {},
        setupFormValidation: function() {},
        setupTooltips: function() {},
        setupModalSystem: function() {},
        setupNotificationSystem: function() {},
        initThemeToggle: function() {},
        trapFocus: function() {},
        releaseFocus: function() {},
        trackVideoCardClick: function() {},
        trackPageView: function() {},
        trackUserEngagement: function() {},
        trackScrollDepth: function() {},
        trackSearchQueries: function() {},
        optimizeImages: function() {},
        deferNonCriticalScripts: function() {},
        initCacheManagement: function() {},
        addSkipToContentLink: function() {},
        enhanceKeyboardNavigation: function() {},
        setupARIALabels: function() {},
        setupFocusManagement: function() {},
        handleReducedMotion: function() {},
        setupInstallPrompt: function() {},
        setupOfflineSupport: function() {},
        setupBackgroundSync: function() {}
    };

    // Initialize theme when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => VideoPlayerTheme.init());
    } else {
        VideoPlayerTheme.init();
    }

    // Make theme globally available
    window.VideoPlayerTheme = VideoPlayerTheme;

})();