<?php
/**
 * 404 Error Page Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <div class="error-404-container">
        <div class="error-animation">
            <div class="error-number">
                <span class="digit">4</span>
                <span class="digit video-icon">📹</span>
                <span class="digit">4</span>
            </div>
            <div class="error-glitch"></div>
        </div>

        <div class="error-content">
            <h1 class="error-title">
                <?php esc_html_e('¡Ups! Video no encontrado', 'videoplayer'); ?>
            </h1>
            
            <p class="error-description">
                <?php esc_html_e('La página que buscas parece haber desaparecido como un video privado. No te preocupes, te ayudamos a encontrar lo que necesitas.', 'videoplayer'); ?>
            </p>
            
            <div class="error-suggestions">
                <h2><?php esc_html_e('¿Qué puedes hacer?', 'videoplayer'); ?></h2>
                
                <div class="suggestions-grid">
                    <div class="suggestion-card">
                        <div class="suggestion-icon">🏠</div>
                        <h3><?php esc_html_e('Ir al Inicio', 'videoplayer'); ?></h3>
                        <p><?php esc_html_e('Vuelve a la página principal para ver los videos más recientes', 'videoplayer'); ?></p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="suggestion-btn">
                            <?php esc_html_e('Página Principal', 'videoplayer'); ?>
                        </a>
                    </div>
                    
                    <div class="suggestion-card">
                        <div class="suggestion-icon">🔍</div>
                        <h3><?php esc_html_e('Buscar Videos', 'videoplayer'); ?></h3>
                        <p><?php esc_html_e('Usa nuestro buscador para encontrar exactamente lo que necesitas', 'videoplayer'); ?></p>
                        <div class="search-container">
                            <?php get_search_form(); ?>
                        </div>
                    </div>
                    
                    <div class="suggestion-card">
                        <div class="suggestion-icon">📹</div>
                        <h3><?php esc_html_e('Explorar Videos', 'videoplayer'); ?></h3>
                        <p><?php esc_html_e('Descubre todos nuestros videos organizados por categorías', 'videoplayer'); ?></p>
                        <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="suggestion-btn">
                            <?php esc_html_e('Ver Videos', 'videoplayer'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Popular Videos -->
            <?php
            $popular_videos = new WP_Query(array(
                'post_type' => 'video',
                'posts_per_page' => 6,
                'meta_key' => '_view_count',
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            ));
            ?>
            
            <?php if ($popular_videos->have_posts()) : ?>
                <div class="popular-videos-section">
                    <h2><?php esc_html_e('O mira estos videos populares:', 'videoplayer'); ?></h2>
                    
                    <div class="popular-videos-grid">
                        <?php while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                            <article class="mini-video-card">
                                <div class="mini-video-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        <?php else : ?>
                                            <div class="thumbnail-placeholder">
                                                <span>▶</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mini-video-overlay">
                                            <div class="mini-play-btn">▶</div>
                                        </div>
                                        
                                        <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                    </a>
                                </div>
                                
                                <div class="mini-video-info">
                                    <h4 class="mini-video-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <div class="mini-video-meta">
                                        <?php echo number_format(get_video_views()); ?> vistas
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Categories -->
            <div class="categories-section">
                <h2><?php esc_html_e('Explora por categorías:', 'videoplayer'); ?></h2>
                
                <div class="categories-grid">
                    <?php 
                    $categories = get_categories(array('number' => 8, 'hide_empty' => true));
                    foreach ($categories as $category) {
                        $category_icon = get_term_meta($category->term_id, 'category_icon', true) ?: '📁';
                        ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-card">
                            <div class="category-icon"><?php echo esc_html($category_icon); ?></div>
                            <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                            <div class="category-count"><?php echo $category->count; ?> videos</div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h2><?php esc_html_e('¿Aún necesitas ayuda?', 'videoplayer'); ?></h2>
                <p><?php esc_html_e('Si crees que esto es un error o necesitas asistencia adicional, no dudes en contactarnos.', 'videoplayer'); ?></p>
                
                <div class="help-actions">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="help-btn">
                        📧 <?php esc_html_e('Contactar Soporte', 'videoplayer'); ?>
                    </a>
                    
                    <button class="report-error-btn" onclick="reportError()">
                        🐛 <?php esc_html_e('Reportar Error', 'videoplayer'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.error-404-container {
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px 0;
}

/* Error Animation */
.error-animation {
    position: relative;
    margin-bottom: 40px;
}

.error-number {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8rem;
    font-weight: 900;
    margin-bottom: 20px;
}

.digit {
    display: inline-block;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: glitch 2s infinite;
    text-shadow: 3px 3px 0 rgba(255, 107, 107, 0.1);
}

.video-icon {
    font-size: 6rem;
    margin: 0 20px;
    animation: bounce 2s infinite;
}

.error-glitch {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 107, 107, 0.1) 50%, transparent 100%);
    animation: scan 3s infinite;
    pointer-events: none;
}

@keyframes glitch {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-2px); }
    40% { transform: translateX(2px); }
    60% { transform: translateX(-1px); }
    80% { transform: translateX(1px); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-20px); }
    60% { transform: translateY(-10px); }
}

@keyframes scan {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Error Content */
.error-content {
    max-width: 800px;
    width: 100%;
}

.error-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: var(--light-text);
}

.error-description {
    font-size: 1.2rem;
    color: var(--muted-text);
    line-height: 1.6;
    margin-bottom: 50px;
}

/* Suggestions */
.error-suggestions {
    margin-bottom: 60px;
}

.error-suggestions h2 {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: var(--primary-color);
}

.suggestions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.suggestion-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 30px 20px;
    transition: var(--transition);
    text-align: center;
}

.suggestion-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    box-shadow: var(--shadow-light);
}

.suggestion-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    display: block;
}

.suggestion-card h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.suggestion-card p {
    color: var(--muted-text);
    line-height: 1.5;
    margin-bottom: 20px;
}

.suggestion-btn {
    display: inline-block;
    background: var(--gradient-primary);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.suggestion-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.search-container {
    margin-top: 15px;
}

/* Popular Videos */
.popular-videos-section {
    margin-bottom: 60px;
}

.popular-videos-section h2 {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: var(--primary-color);
}

.popular-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.mini-video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.mini-video-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.08);
}

.mini-video-thumbnail {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.mini-video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #333, #555);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: rgba(255, 255, 255, 0.6);
}

.mini-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.mini-video-thumbnail:hover .mini-video-overlay {
    opacity: 1;
}

.mini-play-btn {
    background: rgba(255, 255, 255, 0.9);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #333;
}

.video-duration {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
}

.mini-video-info {
    padding: 15px;
}

.mini-video-title {
    font-size: 14px;
    margin-bottom: 8px;
    line-height: 1.3;
}

.mini-video-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.mini-video-title a:hover {
    color: var(--primary-color);
}

.mini-video-meta {
    font-size: 12px;
    color: var(--muted-text);
}

/* Categories */
.categories-section {
    margin-bottom: 60px;
}

.categories-section h2 {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: var(--primary-color);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.category-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 20px;
    text-decoration: none;
    transition: var(--transition);
    text-align: center;
}

.category-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-3px);
}

.category-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.category-name {
    color: var(--light-text);
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 14px;
}

.category-count {
    color: var(--muted-text);
    font-size: 12px;
}

/* Help Section */
.help-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 40px;
    text-align: center;
}

.help-section h2 {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: var(--primary-color);
}

.help-section p {
    color: var(--muted-text);
    margin-bottom: 30px;
    line-height: 1.6;
}

.help-actions {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.help-btn,
.report-error-btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.help-btn {
    background: var(--gradient-primary);
    color: white;
}

.report-error-btn {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    border: 1px solid var(--border-color);
}

.help-btn:hover,
.report-error-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.report-error-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    color: var(--light-text);
}

/* Responsive */
@media (max-width: 768px) {
    .error-number {
        font-size: 5rem;
    }
    
    .video-icon {
        font-size: 4rem;
        margin: 0 10px;
    }
    
    .error-title {
        font-size: 2rem;
    }
    
    .error-description {
        font-size: 1rem;
    }
    
    .suggestions-grid {
        grid-template-columns: 1fr;
    }
    
    .help-actions {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 480px) {
    .error-number {
        font-size: 4rem;
    }
    
    .video-icon {
        font-size: 3rem;
    }
    
    .popular-videos-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Loading animation for suggestions */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.suggestion-card,
.mini-video-card,
.category-card {
    animation: fadeInUp 0.6s ease-out;
}

.suggestion-card:nth-child(1) { animation-delay: 0.1s; }
.suggestion-card:nth-child(2) { animation-delay: 0.2s; }
.suggestion-card:nth-child(3) { animation-delay: 0.3s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track 404 errors
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view', {
            'page_title': '404 Error',
            'page_location': window.location.href,
            'custom_parameter': 'error_404'
        });
    }
    
    // Auto-focus search field if visible
    const searchField = document.querySelector('.search-field');
    if (searchField) {
        setTimeout(() => {
            searchField.focus();
        }, 1000);
    }
    
    // Add click tracking to suggestion buttons
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': '404_help',
                    'event_label': this.textContent.trim()
                });
            }
        });
    });
    
    // Random error messages (optional)
    const errorMessages = [
        '<?php esc_html_e('La página que buscas parece haber desaparecido como un video privado.', 'videoplayer'); ?>',
        '<?php esc_html_e('Este enlace está más perdido que un cable de carga.', 'videoplayer'); ?>',
        '<?php esc_html_e('404: Esta página decidió tomarse unas vacaciones.', 'videoplayer'); ?>',
        '<?php esc_html_e('Parece que este contenido se editó a sí mismo fuera de existencia.', 'videoplayer'); ?>'
    ];
    
    const randomMessage = errorMessages[Math.floor(Math.random() * errorMessages.length)];
    const descriptionElement = document.querySelector('.error-description');
    if (descriptionElement && Math.random() > 0.7) {
        descriptionElement.textContent = randomMessage;
    }
});

function reportError() {
    const currentUrl = window.location.href;
    const userAgent = navigator.userAgent;
    const referrer = document.referrer;
    
    // Create error report
    const errorData = {
        url: currentUrl,
        referrer: referrer,
        userAgent: userAgent,
        timestamp: new Date().toISOString(),
        type: '404_error'
    };
    
    // You can send this to your analytics or error tracking service
    console.log('Error reported:', errorData);
    
    // Show feedback to user
    alert('<?php esc_html_e('Gracias por reportar este error. Nuestro equipo lo revisará pronto.', 'videoplayer'); ?>');
    
    // Track the report
    if (typeof gtag !== 'undefined') {
        gtag('event', 'error_report', {
            'event_category': '404_errors',
            'event_label': currentUrl
        });
    }
}

// Easter egg - Konami code
let konamiCode = [];
const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];

document.addEventListener('keydown', function(e) {
    konamiCode.push(e.keyCode);
    
    if (konamiCode.length > konamiSequence.length) {
        konamiCode.shift();
    }
    
    if (konamiCode.join(',') === konamiSequence.join(',')) {
        // Activate easter egg
        document.body.style.transform = 'rotate(0.5deg)';
        document.body.style.filter = 'hue-rotate(180deg)';
        
        setTimeout(() => {
            document.body.style.transform = '';
            document.body.style.filter = '';
        }, 2000);
        
        konamiCode = [];
    }
});
</script>

<?php get_footer(); ?>