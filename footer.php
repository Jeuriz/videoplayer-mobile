<?php
/**
 * Footer template
 * 
 * @package VideoPlayerMobile
 */
?>

    </main><!-- #main -->

    <footer class="site-footer" role="contentinfo">
        <div class="footer-content">
            <?php if (is_active_sidebar('footer-widgets')) : ?>
                <div class="footer-widgets">
                    <?php dynamic_sidebar('footer-widgets'); ?>
                </div>
            <?php endif; ?>
            
            <div class="footer-info">
                <p class="footer-text">
                    © <?php echo date('Y'); ?> 
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                    <?php esc_html_e('Todos los derechos reservados.', 'videoplayer'); ?>
                </p>
                
                <nav class="footer-links" aria-label="<?php esc_attr_e('Footer Menu', 'videoplayer'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => 'videoplayer_footer_fallback_menu',
                    ));
                    ?>
                </nav>
                
                <?php if (get_theme_mod('show_social_links', true)) : ?>
                    <div class="social-links">
                        <?php if ($facebook = get_theme_mod('facebook_url')) : ?>
                            <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">📘</a>
                        <?php endif; ?>
                        
                        <?php if ($twitter = get_theme_mod('twitter_url')) : ?>
                            <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter">🐦</a>
                        <?php endif; ?>
                        
                        <?php if ($youtube = get_theme_mod('youtube_url')) : ?>
                            <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener" aria-label="YouTube">📺</a>
                        <?php endif; ?>
                        
                        <?php if ($instagram = get_theme_mod('instagram_url')) : ?>
                            <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">📷</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <p class="theme-credit">
                    <?php
                    printf(
                        esc_html__('Tema: %1$s por %2$s.', 'videoplayer'),
                        'VideoPlayer Mobile',
                        '<a href="#" rel="designer">Tu Nombre</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </footer>

</div><!-- .site-container -->

<!-- Back to top button -->
<button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e('Volver arriba', 'videoplayer'); ?>" style="display: none;">
    ↑
</button>

<style>
.footer-widgets {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin-bottom: 30px;
    text-align: left;
}

.footer-widgets .widget {
    background: rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-widgets .widget-title {
    color: var(--primary-color);
    font-size: 16px;
    margin-bottom: 15px;
    font-weight: 600;
}

.footer-widgets .widget ul {
    list-style: none;
    padding: 0;
}

.footer-widgets .widget li {
    margin-bottom: 8px;
}

.footer-widgets .widget a {
    color: var(--muted-text);
    transition: var(--transition);
    display: block;
    padding: 5px 0;
}

.footer-widgets .widget a:hover {
    color: var(--primary-color);
    padding-left: 10px;
}

.footer-menu {
    display: flex;
    justify-content: center;
    gap: 20px;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin: 15px 0;
}

.social-links a {
    font-size: 24px;
    transition: var(--transition);
    display: block;
    padding: 5px;
    border-radius: 8px;
}

.social-links a:hover {
    background: var(--hover-bg);
    transform: translateY(-2px);
}

.theme-credit {
    font-size: 12px;
    color: var(--muted-text);
    margin-top: 15px;
}

.theme-credit a {
    color: var(--primary-color);
}

.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: var(--gradient-primary);
    border: none;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    transition: var(--transition);
    z-index: 100;
    box-shadow: var(--shadow-light);
}

.back-to-top:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-heavy);
}

@media (min-width: 768px) {
    .footer-widgets {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .footer-widgets {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1200px) {
    .footer-widgets {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>

<script>
// Back to top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('back-to-top');
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    
    // Smooth scroll to top
    backToTopButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Mobile menu functionality
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNavigation = document.querySelector('.mobile-navigation');
    const mobileNavClose = document.querySelector('.mobile-nav-close');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    
    if (mobileMenuToggle && mobileNavigation) {
        // Create overlay if it doesn't exist
        if (!mobileMenuOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'mobile-menu-overlay';
            document.body.appendChild(overlay);
        }
        
        // Open mobile menu
        mobileMenuToggle.addEventListener('click', function() {
            mobileNavigation.classList.add('active');
            document.body.style.overflow = 'hidden';
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
            
            // Add overlay styles
            const overlay = document.querySelector('.mobile-menu-overlay');
            if (overlay) {
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 150;
                    opacity: 1;
                    visibility: visible;
                    transition: all 0.3s ease;
                `;
            }
        });
        
        // Close mobile menu function
        function closeMobileMenu() {
            mobileNavigation.classList.remove('active');
            document.body.style.overflow = '';
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            
            const overlay = document.querySelector('.mobile-menu-overlay');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.visibility = 'hidden';
                setTimeout(() => {
                    overlay.style.cssText = '';
                }, 300);
            }
        }
        
        // Close mobile menu events
        if (mobileNavClose) {
            mobileNavClose.addEventListener('click', closeMobileMenu);
        }
        
        // Close on overlay click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('mobile-menu-overlay')) {
                closeMobileMenu();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileNavigation.classList.contains('active')) {
                closeMobileMenu();
            }
        });
        
        // Close on window resize (desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileNavigation.classList.contains('active')) {
                closeMobileMenu();
            }
        });
    }
    
    // Add loading animation to video links
    const videoLinks = document.querySelectorAll('a[href*="/video/"], .video-card, .related-video');
    videoLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Add loading state
            if (!this.classList.contains('loading')) {
                this.classList.add('loading');
                
                // Remove loading state after navigation
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 2000);
            }
        });
    });
});

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Service Worker registration for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('SW registered: ', registration);
        }).catch(function(registrationError) {
            console.log('SW registration failed: ', registrationError);
        });
    });
}
</script>

<?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * Footer fallback menu
 */
function videoplayer_footer_fallback_menu() {
    echo '<div class="footer-menu">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Inicio', 'videoplayer') . '</a>';
    echo '<a href="' . esc_url(get_privacy_policy_url()) . '">' . esc_html__('Privacidad', 'videoplayer') . '</a>';
    echo '<a href="' . esc_url(home_url('/contact/')) . '">' . esc_html__('Contacto', 'videoplayer') . '</a>';
    echo '</div>';
}
?>