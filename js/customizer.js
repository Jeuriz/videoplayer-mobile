/**
 * Customizer Live Preview JavaScript
 * 
 * @package VideoPlayerMobile
 */

(function($) {
    'use strict';

    // Site title and description
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-logo a, .site-title').text(to);
        });
    });

    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Custom logo text
    wp.customize('custom_logo_text', function(value) {
        value.bind(function(to) {
            $('.site-logo').text(to || wp.customize('blogname')());
        });
    });

    // Colors
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            updateCustomCSS('primary_color', to);
        });
    });

    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            updateCustomCSS('secondary_color', to);
        });
    });

    // Header options
    wp.customize('show_search_in_header', function(value) {
        value.bind(function(to) {
            const searchForm = $('.header-search');
            if (to) {
                searchForm.show();
            } else {
                searchForm.hide();
            }
        });
    });

    wp.customize('sticky_header', function(value) {
        value.bind(function(to) {
            const header = $('.site-header');
            if (to) {
                header.addClass('sticky-header');
            } else {
                header.removeClass('sticky-header');
            }
        });
    });

    // Video settings
    wp.customize('show_video_duration', function(value) {
        value.bind(function(to) {
            const durations = $('.video-duration');
            if (to) {
                durations.show();
            } else {
                durations.hide();
            }
        });
    });

    wp.customize('autoplay_videos', function(value) {
        value.bind(function(to) {
            const videos = $('video');
            videos.each(function() {
                if (to) {
                    $(this).attr('autoplay', 'autoplay');
                    $(this).attr('muted', 'muted');
                } else {
                    $(this).removeAttr('autoplay');
                    $(this).removeAttr('muted');
                }
            });
        });
    });

    // Typography
    wp.customize('font_size', function(value) {
        value.bind(function(to) {
            $('html').css('font-size', to + 'px');
        });
    });

    wp.customize('body_font', function(value) {
        value.bind(function(to) {
            updateFontFamily('body', to);
        });
    });

    wp.customize('heading_font', function(value) {
        value.bind(function(to) {
            updateFontFamily('heading', to);
        });
    });

    // Social media links
    const socialNetworks = ['facebook', 'twitter', 'youtube', 'instagram', 'tiktok', 'discord'];
    
    socialNetworks.forEach(function(network) {
        wp.customize(network + '_url', function(value) {
            value.bind(function(to) {
                const socialLink = $('.social-' + network);
                if (to) {
                    socialLink.attr('href', to).show();
                } else {
                    socialLink.hide();
                }
            });
        });
    });

    wp.customize('show_social_links', function(value) {
        value.bind(function(to) {
            const socialLinks = $('.social-links, .social-media-widget');
            if (to) {
                socialLinks.show();
            } else {
                socialLinks.hide();
            }
        });
    });

    // Performance settings
    wp.customize('enable_lazy_loading', function(value) {
        value.bind(function(to) {
            if (to) {
                // Add lazy loading attributes to images
                $('img').each(function() {
                    if (!$(this).attr('data-src') && $(this).attr('src')) {
                        $(this).attr('data-src', $(this).attr('src'));
                        $(this).attr('loading', 'lazy');
                    }
                });
                showCustomizerMessage('Lazy loading habilitado', 'success');
            } else {
                $('img').removeAttr('loading');
                showCustomizerMessage('Lazy loading deshabilitado', 'info');
            }
        });
    });

    // Advanced settings
    wp.customize('custom_css', function(value) {
        value.bind(function(to) {
            updateCustomCSS('custom_css', to);
        });
    });

    wp.customize('enable_debug_mode', function(value) {
        value.bind(function(to) {
            if (to) {
                window.videoPlayerDebug = true;
                console.log('VideoPlayer Debug Mode: Enabled');
                showCustomizerMessage('Modo debug habilitado', 'info');
            } else {
                window.videoPlayerDebug = false;
                console.log('VideoPlayer Debug Mode: Disabled');
                showCustomizerMessage('Modo debug deshabilitado', 'info');
            }
        });
    });

    // Hero section customizations
    wp.customize('hero_title', function(value) {
        value.bind(function(to) {
            $('.hero-title').text(to);
        });
    });

    wp.customize('hero_description', function(value) {
        value.bind(function(to) {
            $('.hero-description').text(to);
        });
    });

    // Utility functions
    function updateCustomCSS(setting, value) {
        let customStyleId = 'videoplayer-customizer-' + setting;
        let existingStyle = $('#' + customStyleId);
        
        // Remove existing style
        if (existingStyle.length) {
            existingStyle.remove();
        }

        let css = '';
        
        switch (setting) {
            case 'primary_color':
                css = `
                    :root {
                        --primary-color: ${value};
                        --gradient-primary: linear-gradient(45deg, ${value}, var(--secondary-color));
                    }
                `;
                break;
                
            case 'secondary_color':
                css = `
                    :root {
                        --secondary-color: ${value};
                        --gradient-primary: linear-gradient(45deg, var(--primary-color), ${value});
                    }
                `;
                break;
                
            case 'custom_css':
                css = value;
                break;
        }

        if (css) {
            $('head').append(`<style id="${customStyleId}">${css}</style>`);
        }
    }

    function updateFontFamily(type, fontFamily) {
        let customStyleId = 'videoplayer-font-' + type;
        let existingStyle = $('#' + customStyleId);
        
        // Remove existing style
        if (existingStyle.length) {
            existingStyle.remove();
        }

        if (fontFamily !== 'system') {
            let css = '';
            
            if (type === 'body') {
                css = `body { font-family: '${fontFamily}', sans-serif; }`;
            } else if (type === 'heading') {
                css = `h1, h2, h3, h4, h5, h6 { font-family: '${fontFamily}', sans-serif; }`;
            }
            
            if (css) {
                $('head').append(`<style id="${customStyleId}">${css}</style>`);
                
                // Load Google Font if needed
                loadGoogleFont(fontFamily);
            }
        }
    }

    function loadGoogleFont(fontFamily) {
        const googleFonts = ['roboto', 'open-sans', 'lato', 'montserrat', 'poppins'];
        
        if (googleFonts.includes(fontFamily.toLowerCase())) {
            const fontName = fontFamily.replace('-', '+');
            const fontUrl = `https://fonts.googleapis.com/css2?family=${fontName}:wght@300;400;500;600;700&display=swap`;
            
            // Check if font is already loaded
            if (!$(`link[href*="${fontName}"]`).length) {
                $('head').append(`<link href="${fontUrl}" rel="stylesheet">`);
            }
        }
    }

    function showCustomizerMessage(message, type = 'info') {
        // Create notification element
        const notification = $(`
            <div class="customizer-notification customizer-notification-${type}">
                <div class="customizer-notification-content">
                    <span class="customizer-notification-icon">${getNotificationIcon(type)}</span>
                    <span class="customizer-notification-message">${message}</span>
                </div>
            </div>
        `);

        // Add styles
        notification.css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: getNotificationColor(type),
            color: 'white',
            padding: '12px 20px',
            borderRadius: '6px',
            boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            zIndex: '999999',
            fontSize: '14px',
            maxWidth: '300px',
            animation: 'slideInRight 0.3s ease-out'
        });

        // Add to page
        $('body').append(notification);

        // Auto remove after 3 seconds
        setTimeout(function() {
            notification.css('animation', 'slideOutRight 0.3s ease-in');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }

    function getNotificationIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }

    function getNotificationColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    }

    // Add CSS animations if not present
    if (!$('#customizer-animations-css').length) {
        $('head').append(`
            <style id="customizer-animations-css">
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                
                .customizer-notification-content {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .sticky-header {
                    position: fixed !important;
                    top: 0 !important;
                    width: 100% !important;
                    z-index: 1000 !important;
                    animation: slideDown 0.3s ease-out;
                }
                
                @keyframes slideDown {
                    from { transform: translateY(-100%); }
                    to { transform: translateY(0); }
                }
            </style>
        `);
    }

    // Video player enhancements for customizer
    function initVideoPlayerCustomizer() {
        // Add customizer-specific enhancements to video players
        $('.video-player').each(function() {
            const player = $(this);
            
            // Add customizer controls overlay
            if (!player.find('.customizer-video-controls').length) {
                const controls = $(`
                    <div class="customizer-video-controls" style="
                        position: absolute;
                        top: 10px;
                        left: 10px;
                        background: rgba(0, 0, 0, 0.8);
                        color: white;
                        padding: 5px 10px;
                        border-radius: 4px;
                        font-size: 12px;
                        z-index: 10;
                        display: none;
                    ">
                        Vista previa - Customizer
                    </div>
                `);
                
                player.append(controls);
                
                player.hover(
                    function() { controls.show(); },
                    function() { controls.hide(); }
                );
            }
        });
    }

    // Theme layout adjustments
    wp.customize('videos_per_page', function(value) {
        value.bind(function(to) {
            $('.videos-grid .video-card').each(function(index) {
                if (index >= to) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            
            showCustomizerMessage(`Mostrando ${to} videos por página`, 'info');
        });
    });

    // Real-time search preview
    wp.customize('search_placeholder', function(value) {
        value.bind(function(to) {
            $('.search-field').attr('placeholder', to);
        });
    });

    // Footer customizations
    wp.customize('footer_text', function(value) {
        value.bind(function(to) {
            $('.footer-text').html(to);
        });
    });

    // Mobile menu customizations
    wp.customize('mobile_menu_style', function(value) {
        value.bind(function(to) {
            const mobileNav = $('.mobile-navigation');
            mobileNav.removeClass('slide-left slide-right slide-up slide-down');
            mobileNav.addClass('slide-' + to);
        });
    });

    // Initialize customizer enhancements
    $(document).ready(function() {
        initVideoPlayerCustomizer();
        
        // Add customizer body class
        $('body').addClass('customizer-preview');
        
        // Enhance form interactions
        $('.comment-form, .search-form, .newsletter-form').addClass('customizer-enhanced');
        
        // Log customizer ready
        if (window.videoPlayerDebug) {
            console.log('VideoPlayer Customizer: Ready');
        }
    });

    // Listen for theme customizer events
    $(document).on('customize-preview-ready', function() {
        // Additional setup when customizer is fully loaded
        showCustomizerMessage('Customizer cargado - Los cambios se ven en tiempo real', 'success');
    });

    // Handle responsive preview changes
    wp.customize.preview.bind('customize-preview-responsive-device', function(device) {
        $('body').removeClass('customizer-desktop customizer-tablet customizer-mobile');
        $('body').addClass('customizer-' + device);
        
        showCustomizerMessage(`Vista: ${device}`, 'info');
    });

    // Advanced color scheme switching
    function applyColorScheme(scheme) {
        const schemes = {
            default: {
                primary: '#ff6b6b',
                secondary: '#4ecdc4'
            },
            ocean: {
                primary: '#3498db',
                secondary: '#2ecc71'
            },
            sunset: {
                primary: '#e74c3c',
                secondary: '#f39c12'
            },
            forest: {
                primary: '#27ae60',
                secondary: '#16a085'
            },
            purple: {
                primary: '#9b59b6',
                secondary: '#8e44ad'
            }
        };

        if (schemes[scheme]) {
            updateCustomCSS('color_scheme', `
                :root {
                    --primary-color: ${schemes[scheme].primary};
                    --secondary-color: ${schemes[scheme].secondary};
                    --gradient-primary: linear-gradient(45deg, ${schemes[scheme].primary}, ${schemes[scheme].secondary});
                }
            `);
        }
    }

    // Expose functions globally for external access
    window.videoPlayerCustomizer = {
        updateCustomCSS: updateCustomCSS,
        updateFontFamily: updateFontFamily,
        showMessage: showCustomizerMessage,
        applyColorScheme: applyColorScheme
    };

})(jQuery);
