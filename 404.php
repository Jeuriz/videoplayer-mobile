<?php
/**
 * 404 Error Page Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <div class="error-404-page">
        
        <!-- Error Animation/Icon -->
        <div class="error-animation">
            <div class="error-number">
                <span class="digit">4</span>
                <div class="video-icon">
                    <div class="screen">
                        <div class="static-lines"></div>
                        <div class="play-button">▶</div>
                    </div>
                </div>
                <span class="digit">4</span>
            </div>
            
            <div class="glitch-effect">
                <span class="glitch-text">ERROR</span>
                <span class="glitch-text">ERROR</span>
                <span class="glitch-text">ERROR</span>
            </div>
        </div>

        <!-- Error Content -->
        <div class="error-content">
            <h1 class="error-title">
                <?php esc_html_e('¡Oops! Página no encontrada', 'videoplayer'); ?>
            </h1>
            
            <p class="error-description">
                <?php esc_html_e('La página que buscas parece haber desaparecido en el ciberespacio. No te preocupes, te ayudamos a encontrar lo que necesitas.', 'videoplayer'); ?>
            </p>

            <!-- Quick Actions -->
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <span class="icon">🏠</span>
                    <?php esc_html_e('Ir al inicio', 'videoplayer'); ?>
                </a>
                
                <button onclick="history.back()" class="btn btn-secondary">
                    <span class="icon">←</span>
                    <?php esc_html_e('Volver atrás', 'videoplayer'); ?>
                </button>
                
                <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="btn btn-accent">
                    <span class="icon">📹</span>
                    <?php esc_html_e('Ver videos', 'videoplayer'); ?>
                </a>
            </div>
        </div>

        <!-- Search Section -->
        <div class="error-search-section">
            <h3 class="search-title">
                <?php esc_html_e('¿Buscabas algo específico?', 'videoplayer'); ?>
            </h3>
            
            <form role="search" method="get" class="error-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-group">
                    <input type="search" 
                           class="search-field" 
                           placeholder="<?php esc_attr_e('Buscar contenido...', 'videoplayer'); ?>" 
                           name="s" 
                           autocomplete="off">
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Buscar', 'videoplayer'); ?>">
                        <span class="icon">🔍</span>
                    </button>
                </div>
                
                <div class="search-filters">
                    <label class="search-filter">
                        <input type="radio" name="post_type" value="" checked>
                        <span><?php esc_html_e('Todo', 'videoplayer'); ?></span>
                    </label>
                    <label class="search-filter">
                        <input type="radio" name="post_type" value="video">
                        <span><?php esc_html_e('Videos', 'videoplayer'); ?></span>
                    </label>
                    <label class="search-filter">
                        <input type="radio" name="post_type" value="post">
                        <span><?php esc_html_e('Artículos', 'videoplayer'); ?></span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Popular Content -->
        <div class="error-suggestions">
            <h3 class="suggestions-title">
                <?php esc_html_e('Contenido popular que te puede interesar', 'videoplayer'); ?>
            </h3>
            
            <div class="suggestions-grid">
                
                <!-- Popular Videos -->
                <div class="suggestion-category">
                    <h4 class="category-title">
                        <span class="icon">🔥</span>
                        <?php esc_html_e('Videos populares', 'videoplayer'); ?>
                    </h4>
                    
                    <?php
                    $popular_videos = new WP_Query(array(
                        'post_type' => 'video',
                        'posts_per_page' => 4,
                        'meta_key' => '_view_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'meta_query' => array(
                            array(
                                'key' => '_view_count',
                                'compare' => 'EXISTS'
                            )
                        )
                    ));
                    
                    if ($popular_videos->have_posts()) :
                    ?>
                        <div class="suggestions-list">
                            <?php while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                                <div class="suggestion-item">
                                    <div class="suggestion-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('thumbnail'); ?>
                                            <?php else : ?>
                                                <div class="thumbnail-placeholder">▶</div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    
                                    <div class="suggestion-content">
                                        <h5 class="suggestion-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h5>
                                        <div class="suggestion-meta">
                                            <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php
                        wp_reset_postdata();
                    else :
                    ?>
                        <p class="no-content"><?php esc_html_e('No hay videos disponibles.', 'videoplayer'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Recent Posts -->
                <div class="suggestion-category">
                    <h4 class="category-title">
                        <span class="icon">📝</span>
                        <?php esc_html_e('Artículos recientes', 'videoplayer'); ?>
                    </h4>
                    
                    <?php
                    $recent_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 4,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($recent_posts->have_posts()) :
                    ?>
                        <div class="suggestions-list">
                            <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <div class="suggestion-item">
                                    <div class="suggestion-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('thumbnail'); ?>
                                            <?php else : ?>
                                                <div class="thumbnail-placeholder">📄</div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    
                                    <div class="suggestion-content">
                                        <h5 class="suggestion-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h5>
                                        <div class="suggestion-meta">
                                            <span class="date"><?php echo get_the_date(); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php
                        wp_reset_postdata();
                    else :
                    ?>
                        <p class="no-content"><?php esc_html_e('No hay artículos disponibles.', 'videoplayer'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Categories -->
                <div class="suggestion-category">
                    <h4 class="category-title">
                        <span class="icon">📁</span>
                        <?php esc_html_e('Explorar categorías', 'videoplayer'); ?>
                    </h4>
                    
                    <?php
                    $categories = get_categories(array(
                        'hide_empty' => true,
                        'number' => 6,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));
                    
                    if ($categories) :
                    ?>
                        <div class="categories-list">
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo get_category_link($category->term_id); ?>" class="category-link">
                                    <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                    <span class="category-count"><?php echo $category->count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="no-content"><?php esc_html_e('No hay categorías disponibles.', 'videoplayer'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="error-help">
            <h3 class="help-title">
                <?php esc_html_e('¿Necesitas ayuda?', 'videoplayer'); ?>
            </h3>
            
            <div class="help-options">
                <div class="help-item">
                    <div class="help-icon">📧</div>
                    <h4><?php esc_html_e('Contacto', 'videoplayer'); ?></h4>
                    <p><?php esc_html_e('Envíanos un mensaje si no encuentras lo que buscas.', 'videoplayer'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('contacto'))); ?>" class="help-link">
                        <?php esc_html_e('Contactar', 'videoplayer'); ?>
                    </a>
                </div>
                
                <div class="help-item">
                    <div class="help-icon">❓</div>
                    <h4><?php esc_html_e('FAQ', 'videoplayer'); ?></h4>
                    <p><?php esc_html_e('Consulta las preguntas frecuentes para resolver dudas comunes.', 'videoplayer'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('faq'))); ?>" class="help-link">
                        <?php esc_html_e('Ver FAQ', 'videoplayer'); ?>
                    </a>
                </div>
                
                <div class="help-item">
                    <div class="help-icon">🗺️</div>
                    <h4><?php esc_html_e('Mapa del sitio', 'videoplayer'); ?></h4>
                    <p><?php esc_html_e('Explora todas las secciones y páginas disponibles.', 'videoplayer'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('sitemap'))); ?>" class="help-link">
                        <?php esc_html_e('Ver mapa', 'videoplayer'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Info for Developers -->
        <?php if (current_user_can('manage_options') && defined('WP_DEBUG') && WP_DEBUG) : ?>
            <div class="debug-info">
                <h4><?php esc_html_e('Información de depuración', 'videoplayer'); ?></h4>
                <div class="debug-details">
                    <p><strong>URL solicitada:</strong> <?php echo esc_html($_SERVER['REQUEST_URI']); ?></p>
                    <p><strong>Referer:</strong> <?php echo esc_html($_SERVER['HTTP_REFERER'] ?? 'Directo'); ?></p>
                    <p><strong>User Agent:</strong> <?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?></p>
                    <p><strong>Timestamp:</strong> <?php echo current_time('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.error-404-page {
    text-align: center;
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 40px;
}

/* Error Animation */
.error-animation {
    margin-bottom: 30px;
}

.error-number {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    font-size: 8rem;
    font-weight: 900;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.digit {
    text-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
    animation: glow 2s ease-in-out infinite alternate;
}

.video-icon {
    position: relative;
    width: 120px;
    height: 80px;
}

.screen {
    width: 100%;
    height: 100%;
    background: linear-gradient(145deg, #1a1a1a, #0d0d0d);
    border-radius: 8px;
    border: 3px solid var(--border-color);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: screenFlicker 3s infinite;
}

.static-lines {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 2px,
        rgba(255, 255, 255, 0.05) 2px,
        rgba(255, 255, 255, 0.05) 4px
    );
    animation: staticMove 0.1s linear infinite;
}

.play-button {
    font-size: 2rem;
    color: var(--primary-color);
    z-index: 2;
    animation: pulse 2s infinite;
}

.glitch-effect {
    position: relative;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 3px;
    color: var(--secondary-color);
}

.glitch-text {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
}

.glitch-text:nth-child(1) {
    color: var(--primary-color);
    animation: glitch1 2s infinite;
}

.glitch-text:nth-child(2) {
    color: var(--secondary-color);
    animation: glitch2 2s infinite;
}

.glitch-text:nth-child(3) {
    color: #ff9800;
    animation: glitch3 2s infinite;
}

/* Error Content */
.error-content {
    max-width: 600px;
    margin: 0 auto;
}

.error-title {
    font-size: 2.5rem;
    color: var(--light-text);
    margin-bottom: 20px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.error-description {
    font-size: 1.2rem;
    color: var(--muted-text);
    line-height: 1.6;
    margin-bottom: 30px;
}

.error-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    border: 1px solid var(--border-color);
}

.btn-accent {
    background: var(--secondary-color);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

/* Search Section */
.error-search-section {
    width: 100%;
    max-width: 600px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 30px;
    border: 1px solid var(--border-color);
}

.search-title {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.search-input-group {
    display: flex;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    overflow: hidden;
    margin-bottom: 15px;
}

.search-field {
    flex: 1;
    background: none;
    border: none;
    color: var(--light-text);
    padding: 15px 25px;
    font-size: 16px;
    outline: none;
}

.search-field::placeholder {
    color: var(--muted-text);
}

.search-submit {
    background: var(--primary-color);
    border: none;
    color: white;
    padding: 15px 25px;
    cursor: pointer;
    transition: var(--transition);
}

.search-submit:hover {
    background: var(--secondary-color);
}

.search-filters {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.search-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--muted-text);
    transition: var(--transition);
}

.search-filter:hover {
    color: var(--light-text);
}

.search-filter input[type="radio"] {
    accent-color: var(--primary-color);
}

/* Suggestions */
.error-suggestions {
    width: 100%;
    max-width: 1000px;
}

.suggestions-title {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 30px;
}

.suggestions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    text-align: left;
}

.suggestion-category {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 25px;
    border: 1px solid var(--border-color);
}

.category-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.3rem;
    color: var(--light-text);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
}

.suggestions-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.suggestion-item {
    display: flex;
    gap: 12px;
    align-items: center;
}

.suggestion-thumbnail {
    width: 60px;
    height: 45px;
    border-radius: 6px;
    overflow: hidden;
    flex-shrink: 0;
    background: var(--darker-bg);
}

.suggestion-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.2rem;
}

.suggestion-content {
    flex: 1;
    min-width: 0;
}

.suggestion-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    line-height: 1.3;
}

.suggestion-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.suggestion-title a:hover {
    color: var(--primary-color);
}

.suggestion-meta {
    font-size: 12px;
    color: var(--muted-text);
}

.categories-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.category-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--light-text);
    text-decoration: none;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.category-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--primary-color);
}

.category-count {
    background: var(--primary-color);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.no-content {
    color: var(--muted-text);
    font-style: italic;
    text-align: center;
    padding: 20px;
}

/* Help Section */
.error-help {
    width: 100%;
    max-width: 800px;
}

.help-title {
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 25px;
}

.help-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    text-align: center;
}

.help-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 25px;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.help-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-3px);
}

.help-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.help-item h4 {
    color: var(--light-text);
    margin-bottom: 10px;
}

.help-item p {
    color: var(--muted-text);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.help-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.help-link:hover {
    color: var(--secondary-color);
}

/* Debug Info */
.debug-info {
    width: 100%;
    max-width: 600px;
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: var(--border-radius);
    padding: 20px;
    text-align: left;
    margin-top: 30px;
}

.debug-info h4 {
    color: #ffc107;
    margin-bottom: 15px;
}

.debug-details p {
    color: var(--muted-text);
    font-size: 12px;
    line-height: 1.4;
    margin-bottom: 8px;
    word-break: break-all;
}

/* Animations */
@keyframes glow {
    from { text-shadow: 0 0 20px rgba(255, 107, 107, 0.5); }
    to { text-shadow: 0 0 30px rgba(255, 107, 107, 0.8), 0 0 40px rgba(255, 107, 107, 0.3); }
}

@keyframes screenFlicker {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
    97% { opacity: 1; }
    98% { opacity: 0.9; }
    99% { opacity: 1; }
}

@keyframes staticMove {
    0% { transform: translateY(0); }
    100% { transform: translateY(4px); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

@keyframes glitch1 {
    0%, 100% { transform: translateX(-50%); }
    20% { transform: translateX(-50%) skew(-5deg); }
    40% { transform: translateX(-50%) skew(5deg); }
    60% { transform: translateX(-48%); }
    80% { transform: translateX(-52%); }
}

@keyframes glitch2 {
    0%, 100% { transform: translateX(-50%); }
    30% { transform: translateX(-48%) skew(2deg); }
    50% { transform: translateX(-52%) skew(-2deg); }
    70% { transform: translateX(-50%); }
}

@keyframes glitch3 {
    0%, 100% { transform: translateX(-50%); }
    10% { transform: translateX(-49%); }
    25% { transform: translateX(-51%); }
    75% { transform: translateX(-52%); }
    90% { transform: translateX(-48%); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .error-number {
        font-size: 4rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .video-icon {
        width: 80px;
        height: 60px;
    }
    
    .error-title {
        font-size: 1.8rem;
    }
    
    .error-description {
        font-size: 1rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 200px;
        justify-content: center;
    }
    
    .suggestions-grid {
        grid-template-columns: 1fr;
    }
    
    .help-options {
        grid-template-columns: 1fr;
    }
    
    .search-filters {
        flex-direction: column;
        gap: 10px;
    }
}

@media (max-width: 480px) {
    .error-number {
        font-size: 3rem;
    }
    
    .error-title {
        font-size: 1.5rem;
    }
    
    .error-search-section,
    .suggestion-category,
    .help-item {
        padding: 20px;
    }
    
    .search-input-group {
        flex-direction: column;
        border-radius: var(--border-radius);
    }
    
    .search-field {
        border-bottom: 1px solid var(--border-color);
    }
    
    .search-submit {
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactive effects
    const digits = document.querySelectorAll('.digit');
    digits.forEach((digit, index) => {
        digit.style.animationDelay = `${index * 0.2}s`;
    });
    
    // Random glitch effect
    const glitchTexts = document.querySelectorAll('.glitch-text');
    setInterval(() => {
        glitchTexts.forEach(text => {
            if (Math.random() > 0.8) {
                text.style.transform = `translateX(-50%) skew(${Math.random() * 10 - 5}deg)`;
                setTimeout(() => {
                    text.style.transform = 'translateX(-50%)';
                }, 100);
            }
        });
    }, 2000);
    
    // Track 404 for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view', {
            'page_title': '404 Error',
            'page_location': window.location.href,
            'custom_map': {'custom_parameter_1': 'error_404'}
        });
    }
});
</script>

<?php get_footer(); ?>