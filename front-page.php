<?php
/**
 * Front Page Template - Custom homepage for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="homepage-container">
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <?php 
                    $hero_title = get_theme_mod('hero_title', get_bloginfo('name'));
                    echo esc_html($hero_title);
                    ?>
                </h1>
                
                <p class="hero-description">
                    <?php 
                    $hero_description = get_theme_mod('hero_description', get_bloginfo('description'));
                    echo esc_html($hero_description);
                    ?>
                </p>
                
                <div class="hero-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="cta-button primary">
                        <?php esc_html_e('Explorar Videos', 'videoplayer'); ?>
                    </a>
                    
                    <a href="#featured-videos" class="cta-button secondary scroll-to">
                        <?php esc_html_e('Ver Destacados', 'videoplayer'); ?>
                    </a>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="hero-video-preview">
                    <?php
                    $hero_video = get_theme_mod('hero_featured_video');
                    if ($hero_video) :
                        $video_post = get_post($hero_video);
                        if ($video_post) :
                    ?>
                        <div class="hero-video-card" onclick="location.href='<?php echo get_permalink($video_post); ?>'">
                            <?php if (has_post_thumbnail($video_post)) : ?>
                                <?php echo get_the_post_thumbnail($video_post, 'large', array('class' => 'hero-video-thumbnail')); ?>
                            <?php else : ?>
                                <div class="hero-video-placeholder">
                                    <span class="hero-play-icon">📹</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="hero-video-overlay">
                                <div class="hero-play-btn">
                                    <div class="play-icon-large">▶</div>
                                </div>
                            </div>
                            
                            <div class="hero-video-info">
                                <h3><?php echo get_the_title($video_post); ?></h3>
                                <div class="hero-video-meta">
                                    <span><?php echo number_format(get_video_views($video_post->ID)); ?> vistas</span>
                                    <span><?php echo esc_html(get_video_duration($video_post->ID)); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    else : 
                    ?>
                        <div class="hero-placeholder">
                            <div class="placeholder-icon">🎬</div>
                            <p><?php esc_html_e('Videos increíbles te esperan', 'videoplayer'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?php echo wp_count_posts('video')->publish; ?></div>
                <div class="stat-label"><?php esc_html_e('Videos', 'videoplayer'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="total-views-hero">
                    <?php
                    global $wpdb;
                    $total_views = $wpdb->get_var("
                        SELECT SUM(meta_value) 
                        FROM {$wpdb->postmeta} 
                        WHERE meta_key = '_view_count'
                    ");
                    echo number_format($total_views ?: 0);
                    ?>
                </div>
                <div class="stat-label"><?php esc_html_e('Visualizaciones', 'videoplayer'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number"><?php echo get_categories(array('hide_empty' => true, 'number' => '', 'fields' => 'count')); ?></div>
                <div class="stat-label"><?php esc_html_e('Categorías', 'videoplayer'); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="active-users">...</div>
                <div class="stat-label"><?php esc_html_e('Activos', 'videoplayer'); ?></div>
            </div>
        </div>
    </section>

    <!-- Featured Videos Section -->
    <?php
    $featured_videos = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => 8,
        'meta_query' => array(
            array(
                'key' => '_featured_video',
                'value' => '1',
                'compare' => '='
            )
        )
    ));
    
    if (!$featured_videos->have_posts()) {
        // Fallback to recent videos
        $featured_videos = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => 8,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
    }
    ?>

    <?php if ($featured_videos->have_posts()) : ?>
    <section id="featured-videos" class="featured-videos-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    🌟 <?php esc_html_e('Videos Destacados', 'videoplayer'); ?>
                </h2>
                <p class="section-description">
                    <?php esc_html_e('Los mejores videos seleccionados especialmente para ti', 'videoplayer'); ?>
                </p>
            </div>
            
            <div class="featured-videos-grid">
                <?php while ($featured_videos->have_posts()) : $featured_videos->the_post(); ?>
                    <article class="featured-video-card fade-in-up">
                        <div class="video-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large'); ?>
                                <?php else : ?>
                                    <div class="thumbnail-placeholder">
                                        <span class="video-placeholder-icon">🎬</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="video-overlay">
                                    <div class="play-btn-overlay">
                                        <div class="play-icon">▶</div>
                                    </div>
                                </div>
                                
                                <div class="video-badges">
                                    <span class="duration-badge"><?php echo esc_html(get_video_duration()); ?></span>
                                    <?php if (is_redirect_enabled()) : ?>
                                        <span class="featured-badge">⭐</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        
                        <div class="video-content">
                            <h3 class="video-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="video-meta">
                                <span class="views">👁️ <?php echo number_format(get_video_views()); ?></span>
                                <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                            </div>
                            
                            <?php if (has_excerpt()) : ?>
                                <p class="video-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            
            <div class="section-footer">
                <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="view-all-btn">
                    <?php esc_html_e('Ver Todos los Videos', 'videoplayer'); ?> →
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Popular Categories Section -->
    <?php
    $popular_categories = get_categories(array(
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 6,
        'hide_empty' => true
    ));
    ?>

    <?php if (!empty($popular_categories)) : ?>
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    📁 <?php esc_html_e('Categorías Populares', 'videoplayer'); ?>
                </h2>
                <p class="section-description">
                    <?php esc_html_e('Descubre contenido organizado por temas', 'videoplayer'); ?>
                </p>
            </div>
            
            <div class="categories-grid">
                <?php foreach ($popular_categories as $category) : ?>
                    <div class="category-card fade-in-up">
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                            <div class="category-icon">
                                <?php
                                $category_icon = get_term_meta($category->term_id, 'category_icon', true);
                                echo $category_icon ? esc_html($category_icon) : '📹';
                                ?>
                            </div>
                            
                            <div class="category-info">
                                <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                                <p class="category-description">
                                    <?php 
                                    $description = category_description($category->term_id);
                                    echo $description ? wp_trim_words(strip_tags($description), 8) : sprintf(__('%d videos disponibles', 'videoplayer'), $category->count);
                                    ?>
                                </p>
                                <div class="category-count"><?php echo $category->count; ?> videos</div>
                            </div>
                            
                            <div class="category-arrow">→</div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest Videos Section -->
    <?php
    $latest_videos = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => wp_list_pluck($featured_videos->posts, 'ID')
    ));
    ?>

    <?php if ($latest_videos->have_posts()) : ?>
    <section class="latest-videos-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    🆕 <?php esc_html_e('Últimos Videos', 'videoplayer'); ?>
                </h2>
                <p class="section-description">
                    <?php esc_html_e('Contenido fresco recién agregado', 'videoplayer'); ?>
                </p>
            </div>
            
            <div class="latest-videos-list">
                <?php while ($latest_videos->have_posts()) : $latest_videos->the_post(); ?>
                    <article class="latest-video-item fade-in-up">
                        <div class="video-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <div class="thumbnail-placeholder">
                                        <span>📹</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="video-overlay">
                                    <div class="play-btn-small">▶</div>
                                </div>
                                
                                <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                            </a>
                        </div>
                        
                        <div class="video-info">
                            <h4 class="video-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                            
                            <div class="video-meta">
                                <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                                <span class="date"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                            </div>
                            
                            <?php if (has_excerpt()) : ?>
                                <p class="video-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2 class="newsletter-title">
                        📧 <?php esc_html_e('¡No te pierdas nada!', 'videoplayer'); ?>
                    </h2>
                    <p class="newsletter-description">
                        <?php esc_html_e('Suscríbete para recibir notificaciones de nuevos videos y contenido exclusivo.', 'videoplayer'); ?>
                    </p>
                </div>
                
                <form class="newsletter-form" action="#" method="post">
                    <div class="form-group">
                        <input type="email" 
                               class="newsletter-email" 
                               placeholder="<?php esc_attr_e('tu@email.com', 'videoplayer'); ?>" 
                               required>
                        <button type="submit" class="newsletter-submit">
                            <?php esc_html_e('Suscribirse', 'videoplayer'); ?>
                        </button>
                    </div>
                    
                    <div class="newsletter-privacy">
                        <small>
                            <?php esc_html_e('🔒 Tu privacidad es importante. No compartimos tu email.', 'videoplayer'); ?>
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    <?php esc_html_e('¿Listo para comenzar?', 'videoplayer'); ?>
                </h2>
                <p class="cta-description">
                    <?php esc_html_e('Explora nuestra colección completa de videos y encuentra exactamente lo que buscas.', 'videoplayer'); ?>
                </p>
                
                <div class="cta-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="cta-button primary large">
                        🚀 <?php esc_html_e('Explorar Ahora', 'videoplayer'); ?>
                    </a>
                    
                    <?php if (get_categories()) : ?>
                        <a href="<?php echo esc_url(get_category_link(get_categories()[0]->term_id)); ?>" class="cta-button secondary large">
                            📂 <?php esc_html_e('Ver Categorías', 'videoplayer'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
.homepage-container {
    overflow-x: hidden;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(78, 205, 196, 0.1));
    padding: 60px 20px 40px;
    text-align: center;
    position: relative;
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 40px;
    align-items: center;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 20px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.hero-description {
    font-size: 1.2rem;
    color: var(--muted-text);
    margin-bottom: 30px;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-button {
    padding: 15px 30px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.cta-button.primary {
    background: var(--gradient-primary);
    color: white;
}

.cta-button.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    border: 1px solid var(--border-color);
}

.cta-button.large {
    padding: 18px 36px;
    font-size: 18px;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-light);
}

.hero-video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    cursor: pointer;
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
}

.hero-video-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-heavy);
}

.hero-video-thumbnail {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.hero-video-placeholder {
    height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
    font-size: 4rem;
}

.hero-video-overlay {
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

.hero-video-card:hover .hero-video-overlay {
    opacity: 1;
}

.hero-play-btn {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    font-size: 24px;
}

.hero-video-info {
    padding: 20px;
}

.hero-video-info h3 {
    margin-bottom: 10px;
    font-size: 18px;
}

.hero-video-meta {
    display: flex;
    gap: 15px;
    color: var(--muted-text);
    font-size: 14px;
}

.hero-placeholder {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted-text);
}

.placeholder-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

/* Stats Bar */
.stats-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: var(--muted-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Sections */
.featured-videos-section,
.categories-section,
.latest-videos-section,
.newsletter-section,
.cta-section {
    padding: 60px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-title {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.section-description {
    color: var(--muted-text);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.section-footer {
    text-align: center;
    margin-top: 40px;
}

.view-all-btn {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    font-size: 18px;
    transition: var(--transition);
}

.view-all-btn:hover {
    color: var(--secondary-color);
}

/* Featured Videos Grid */
.featured-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.featured-video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.featured-video-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.08);
}

.featured-video-card .video-thumbnail {
    position: relative;
    height: 180px;
}

.featured-video-card .video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-badges {
    position: absolute;
    bottom: 8px;
    right: 8px;
    display: flex;
    gap: 5px;
}

.duration-badge,
.featured-badge {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.featured-badge {
    background: var(--gradient-primary);
}

.video-content {
    padding: 20px;
}

.video-title {
    font-size: 16px;
    margin-bottom: 10px;
}

.video-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.video-title a:hover {
    color: var(--primary-color);
}

.video-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 13px;
    color: var(--muted-text);
}

.video-excerpt {
    color: var(--muted-text);
    font-size: 14px;
    line-height: 1.5;
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.category-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
}

.category-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.08);
}

.category-card a {
    text-decoration: none;
    color: inherit;
}

.category-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.category-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--light-text);
}

.category-description {
    color: var(--muted-text);
    margin-bottom: 15px;
    line-height: 1.5;
}

.category-count {
    font-size: 12px;
    color: var(--primary-color);
    font-weight: 600;
}

.category-arrow {
    position: absolute;
    top: 20px;
    right: 20px;
    color: var(--muted-text);
    font-size: 18px;
    opacity: 0;
    transition: var(--transition);
}

.category-card:hover .category-arrow {
    opacity: 1;
    transform: translateX(5px);
}

/* Latest Videos List */
.latest-videos-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.latest-video-item {
    display: flex;
    gap: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 15px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.latest-video-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.latest-video-item .video-thumbnail {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
}

.latest-video-item .video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.play-btn-small {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    opacity: 0;
    transition: var(--transition);
}

.latest-video-item:hover .play-btn-small {
    opacity: 1;
}

.video-info {
    flex: 1;
}

.video-info .video-title {
    font-size: 14px;
    margin-bottom: 8px;
}

.video-info .video-meta {
    font-size: 12px;
    margin-bottom: 8px;
}

.video-info .video-excerpt {
    font-size: 12px;
}

/* Newsletter Section */
.newsletter-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
}

.newsletter-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.newsletter-title {
    font-size: 2rem;
    margin-bottom: 15px;
}

.newsletter-description {
    color: var(--muted-text);
    margin-bottom: 30px;
    line-height: 1.6;
}

.newsletter-form .form-group {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.newsletter-email {
    flex: 1;
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 15px;
    color: var(--light-text);
    font-size: 16px;
}

.newsletter-submit {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 15px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: var(--transition);
}

.newsletter-submit:hover {
    transform: translateY(-2px);
}

.newsletter-privacy {
    color: var(--muted-text);
    font-size: 13px;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(78, 205, 196, 0.1));
    text-align: center;
}

.cta-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.cta-description {
    color: var(--muted-text);
    font-size: 1.1rem;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Animations */
.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.fade-in-up:nth-child(1) { animation-delay: 0.1s; }
.fade-in-up:nth-child(2) { animation-delay: 0.2s; }
.fade-in-up:nth-child(3) { animation-delay: 0.3s; }
.fade-in-up:nth-child(4) { animation-delay: 0.4s; }
.fade-in-up:nth-child(5) { animation-delay: 0.5s; }
.fade-in-up:nth-child(6) { animation-delay: 0.6s; }

/* Responsive */
@media (min-width: 768px) {
    .hero-content {
        grid-template-columns: 1fr 1fr;
        text-align: left;
    }
    
    .hero-actions {
        justify-content: flex-start;
    }
    
    .stats-bar {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .newsletter-form .form-group {
        max-width: 400px;
        margin: 0 auto 15px;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .stats-bar {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .featured-videos-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .newsletter-form .form-group {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .latest-video-item {
        flex-direction: column;
    }
    
    .latest-video-item .video-thumbnail {
        width: 100%;
        height: 150px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate counters
    animateCounters();
    
    // Smooth scroll for anchor links
    setupSmoothScroll();
    
    // Newsletter form
    setupNewsletterForm();
    
    // Intersection Observer for animations
    setupScrollAnimations();
    
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/,/g, ''));
            if (isNaN(target)) return;
            
            const duration = 2000;
            const steps = 60;
            const stepValue = target / steps;
            const stepDuration = duration / steps;
            
            let current = 0;
            counter.textContent = '0';
            
            const timer = setInterval(() => {
                current += stepValue;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString();
            }, stepDuration);
        });
        
        // Simulate active users
        const activeUsersElement = document.getElementById('active-users');
        if (activeUsersElement) {
            const baseUsers = 25;
            const variance = 10;
            const randomUsers = Math.floor(baseUsers + (Math.random() * variance * 2) - variance);
            activeUsersElement.textContent = randomUsers;
        }
    }
    
    function setupSmoothScroll() {
        const scrollLinks = document.querySelectorAll('.scroll-to');
        
        scrollLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    function setupNewsletterForm() {
        const newsletterForm = document.querySelector('.newsletter-form');
        
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = this.querySelector('.newsletter-email').value;
                const submitBtn = this.querySelector('.newsletter-submit');
                
                if (!email) return;
                
                // Show loading state
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Suscribiendo...';
                submitBtn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    submitBtn.textContent = '✓ ¡Suscrito!';
                    submitBtn.style.background = '#28a745';
                    
                    // Reset form
                    setTimeout(() => {
                        this.reset();
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.background = '';
                    }, 2000);
                }, 1500);
            });
        }
    }
    
    function setupScrollAnimations() {
        const animatedElements = document.querySelectorAll('.fade-in-up');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    }
});
</script>

<?php get_footer(); ?>