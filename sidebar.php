<?php
/**
 * Sidebar template
 * 
 * @package VideoPlayerMobile
 */

// Check if sidebar has widgets
if (!is_active_sidebar('sidebar-1') && !is_active_sidebar('video-sidebar')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar" role="complementary">
    
    <!-- Search Widget (Always visible) -->
    <div class="widget widget-search">
        <h3 class="widget-title"><?php esc_html_e('Buscar Videos', 'videoplayer'); ?></h3>
        <div class="widget-content">
            <?php get_search_form(); ?>
        </div>
    </div>

    <!-- Popular Videos Widget -->
    <?php
    $popular_videos = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => 5,
        'meta_key' => '_view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'post_status' => 'publish'
    ));
    ?>

    <?php if ($popular_videos->have_posts()) : ?>
        <div class="widget widget-popular-videos">
            <h3 class="widget-title">
                🔥 <?php esc_html_e('Videos Populares', 'videoplayer'); ?>
            </h3>
            <div class="widget-content">
                <div class="popular-videos-list">
                    <?php $rank = 1; while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                        <article class="popular-video-item">
                            <div class="video-rank"><?php echo $rank; ?></div>
                            
                            <div class="video-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="thumbnail-placeholder">
                                            <span>▶</span>
                                        </div>
                                    <?php endif; ?>
                                    
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
                            </div>
                        </article>
                    <?php $rank++; endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                
                <div class="widget-footer">
                    <a href="<?php echo esc_url(add_query_arg('orderby', 'popular', get_post_type_archive_link('video'))); ?>" class="view-all-btn">
                        <?php esc_html_e('Ver todos los populares', 'videoplayer'); ?> →
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Videos Widget -->
    <?php
    $recent_videos = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => 4,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'post__not_in' => array(get_the_ID())
    ));
    ?>

    <?php if ($recent_videos->have_posts()) : ?>
        <div class="widget widget-recent-videos">
            <h3 class="widget-title">
                🆕 <?php esc_html_e('Videos Recientes', 'videoplayer'); ?>
            </h3>
            <div class="widget-content">
                <div class="recent-videos-list">
                    <?php while ($recent_videos->have_posts()) : $recent_videos->the_post(); ?>
                        <article class="recent-video-item">
                            <div class="video-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="thumbnail-placeholder">
                                            <span>▶</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="video-overlay">
                                        <div class="play-btn">▶</div>
                                    </div>
                                    
                                    <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                </a>
                            </div>
                            
                            <div class="video-info">
                                <h4 class="video-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="video-meta">
                                    <span class="date"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Categories Widget -->
    <?php
    $video_categories = get_categories(array(
        'taxonomy' => 'category',
        'hide_empty' => true,
        'number' => 8,
        'orderby' => 'count',
        'order' => 'DESC'
    ));
    ?>

    <?php if (!empty($video_categories)) : ?>
        <div class="widget widget-categories">
            <h3 class="widget-title">
                📁 <?php esc_html_e('Categorías', 'videoplayer'); ?>
            </h3>
            <div class="widget-content">
                <div class="categories-grid">
                    <?php foreach ($video_categories as $category) : ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-item">
                            <div class="category-icon">
                                <?php
                                // Get category icon from custom field or use default
                                $category_icon = get_term_meta($category->term_id, 'category_icon', true);
                                echo $category_icon ? esc_html($category_icon) : '📹';
                                ?>
                            </div>
                            <div class="category-info">
                                <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                <span class="category-count"><?php echo $category->count; ?> videos</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Live Stats Widget -->
    <div class="widget widget-live-stats">
        <h3 class="widget-title">
            📊 <?php esc_html_e('Estadísticas en Vivo', 'videoplayer'); ?>
        </h3>
        <div class="widget-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" id="total-videos"><?php echo wp_count_posts('video')->publish; ?></div>
                    <div class="stat-label"><?php esc_html_e('Videos Totales', 'videoplayer'); ?></div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number" id="total-views">
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
                    <div class="stat-number" id="online-users">
                        <span class="loading-dots">...</span>
                    </div>
                    <div class="stat-label"><?php esc_html_e('Usuarios Online', 'videoplayer'); ?></div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number" id="total-comments"><?php echo wp_count_comments()->approved; ?></div>
                    <div class="stat-label"><?php esc_html_e('Comentarios', 'videoplayer'); ?></div>
                </div>
            </div>
            
            <div class="live-indicator">
                <span class="live-dot"></span>
                <?php esc_html_e('Actualización en tiempo real', 'videoplayer'); ?>
            </div>
        </div>
    </div>

    <!-- Tags Cloud Widget -->
    <?php
    $popular_tags = get_tags(array(
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 20,
        'hide_empty' => true
    ));
    ?>

    <?php if (!empty($popular_tags)) : ?>
        <div class="widget widget-tags-cloud">
            <h3 class="widget-title">
                🏷️ <?php esc_html_e('Tags Populares', 'videoplayer'); ?>
            </h3>
            <div class="widget-content">
                <div class="tags-cloud">
                    <?php foreach ($popular_tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                           class="tag-item" 
                           style="font-size: <?php echo min(16, 10 + ($tag->count * 2)); ?>px;"
                           title="<?php echo $tag->count; ?> videos">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Newsletter Subscription Widget -->
    <div class="widget widget-newsletter">
        <h3 class="widget-title">
            📧 <?php esc_html_e('Mantente Actualizado', 'videoplayer'); ?>
        </h3>
        <div class="widget-content">
            <p class="newsletter-description">
                <?php esc_html_e('Recibe notificaciones de nuevos videos directamente en tu email.', 'videoplayer'); ?>
            </p>
            
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
                        <?php esc_html_e('No spam. Puedes cancelar cuando quieras.', 'videoplayer'); ?>
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Widgets Area -->
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php endif; ?>

    <!-- Video-specific Widgets Area -->
    <?php if (is_singular('video') && is_active_sidebar('video-sidebar')) : ?>
        <?php dynamic_sidebar('video-sidebar'); ?>
    <?php endif; ?>

    <!-- Social Media Widget -->
    <div class="widget widget-social-media">
        <h3 class="widget-title">
            🌐 <?php esc_html_e('Síguenos', 'videoplayer'); ?>
        </h3>
        <div class="widget-content">
            <div class="social-links-grid">
                <?php
                $social_links = array(
                    'youtube' => get_theme_mod('youtube_url', '#'),
                    'facebook' => get_theme_mod('facebook_url', '#'),
                    'twitter' => get_theme_mod('twitter_url', '#'),
                    'instagram' => get_theme_mod('instagram_url', '#'),
                    'tiktok' => get_theme_mod('tiktok_url', '#'),
                    'discord' => get_theme_mod('discord_url', '#')
                );
                
                $social_icons = array(
                    'youtube' => '📺',
                    'facebook' => '📘',
                    'twitter' => '🐦',
                    'instagram' => '📷',
                    'tiktok' => '🎵',
                    'discord' => '💬'
                );
                
                foreach ($social_links as $platform => $url) :
                    if ($url && $url !== '#') :
                ?>
                    <a href="<?php echo esc_url($url); ?>" 
                       class="social-link social-<?php echo $platform; ?>"
                       target="_blank" 
                       rel="noopener"
                       title="<?php echo ucfirst($platform); ?>">
                        <span class="social-icon"><?php echo $social_icons[$platform]; ?></span>
                        <span class="social-label"><?php echo ucfirst($platform); ?></span>
                    </a>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </div>

</aside><!-- #secondary -->

<style>
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 30px;
    padding: 20px 0;
}

.widget {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
}

.widget:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.widget-title {
    background: rgba(255, 255, 255, 0.05);
    color: var(--primary-color);
    font-size: 16px;
    font-weight: 600;
    padding: 15px 20px;
    margin: 0;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.widget-content {
    padding: 20px;
}

/* Search Widget */
.widget-search .search-form {
    margin: 0;
}

/* Popular Videos Widget */
.popular-videos-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.popular-video-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    transition: var(--transition);
}

.popular-video-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.video-rank {
    background: var(--gradient-primary);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

.video-thumbnail {
    position: relative;
    width: 80px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
    flex-shrink: 0;
}

.video-thumbnail img {
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
    color: rgba(255, 255, 255, 0.6);
    font-size: 16px;
}

.video-overlay {
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

.video-thumbnail:hover .video-overlay {
    opacity: 1;
}

.play-btn {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.video-duration {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 4px;
    border-radius: 2px;
    font-size: 10px;
}

.video-info {
    flex: 1;
    min-width: 0;
}

.video-title {
    font-size: 13px;
    margin: 0 0 8px;
    line-height: 1.3;
}

.video-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-title a:hover {
    color: var(--primary-color);
}

.video-meta {
    font-size: 11px;
    color: var(--muted-text);
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.widget-footer {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.view-all-btn {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: var(--transition);
}

.view-all-btn:hover {
    color: var(--secondary-color);
}

/* Recent Videos Widget */
.recent-videos-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.recent-video-item {
    display: flex;
    gap: 10px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
    transition: var(--transition);
}

.recent-video-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Categories Widget */
.categories-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
    text-decoration: none;
    transition: var(--transition);
}

.category-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.category-icon {
    font-size: 18px;
    width: 24px;
    text-align: center;
}

.category-info {
    flex: 1;
}

.category-name {
    display: block;
    color: var(--light-text);
    font-weight: 500;
    font-size: 13px;
}

.category-count {
    display: block;
    color: var(--muted-text);
    font-size: 11px;
}

/* Live Stats Widget */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px 10px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.stat-number {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 11px;
    color: var(--muted-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.live-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 11px;
    color: var(--muted-text);
    padding: 8px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: #00ff00;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading-dots {
    animation: loadingDots 1.5s infinite;
}

@keyframes loadingDots {
    0%, 20% { content: '.'; }
    40% { content: '..'; }
    60%, 100% { content: '...'; }
}

/* Tags Cloud Widget */
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-item {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 4px 10px;
    border-radius: 15px;
    text-decoration: none;
    font-size: 12px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.tag-item:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

/* Newsletter Widget */
.newsletter-description {
    color: var(--muted-text);
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 20px;
}

.newsletter-form .form-group {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
}

.newsletter-email {
    flex: 1;
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 10px 12px;
    color: var(--light-text);
    font-size: 13px;
}

.newsletter-email:focus {
    outline: none;
    border-color: var(--primary-color);
}

.newsletter-submit {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: var(--transition);
}

.newsletter-submit:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-light);
}

.newsletter-privacy {
    text-align: center;
    color: var(--muted-text);
}

/* Social Media Widget */
.social-links-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.social-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.social-icon {
    font-size: 16px;
}

.social-label {
    color: var(--light-text);
    font-size: 12px;
    font-weight: 500;
}

.social-youtube:hover { background: rgba(255, 0, 0, 0.2); }
.social-facebook:hover { background: rgba(24, 119, 242, 0.2); }
.social-twitter:hover { background: rgba(29, 161, 242, 0.2); }
.social-instagram:hover { background: rgba(225, 48, 108, 0.2); }
.social-tiktok:hover { background: rgba(0, 0, 0, 0.3); }
.social-discord:hover { background: rgba(114, 137, 218, 0.2); }

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        padding: 15px 0;
        gap: 20px;
    }
    
    .widget-content {
        padding: 15px;
    }
    
    .video-thumbnail {
        width: 60px;
        height: 45px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .social-links-grid {
        grid-template-columns: 1fr;
    }
    
    .newsletter-form .form-group {
        flex-direction: column;
    }
    
    .categories-grid {
        gap: 6px;
    }
}

@media (max-width: 480px) {
    .popular-video-item,
    .recent-video-item {
        padding: 8px;
        gap: 8px;
    }
    
    .video-title {
        font-size: 12px;
    }
    
    .category-item {
        padding: 10px;
    }
}

/* Widget Animation */
.widget {
    animation: fadeInUp 0.6s ease-out;
}

.widget:nth-child(1) { animation-delay: 0.1s; }
.widget:nth-child(2) { animation-delay: 0.2s; }
.widget:nth-child(3) { animation-delay: 0.3s; }
.widget:nth-child(4) { animation-delay: 0.4s; }
.widget:nth-child(5) { animation-delay: 0.5s; }

/* Dark/Light mode adjustments */
@media (prefers-color-scheme: light) {
    .thumbnail-placeholder {
        background: linear-gradient(45deg, #e0e0e0, #f5f5f5);
        color: #666;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update online users count (simulated)
    updateOnlineUsers();
    
    // Auto-refresh stats periodically
    setInterval(updateLiveStats, 30000); // Every 30 seconds
    
    // Newsletter form handler
    setupNewsletterForm();
    
    // Animate stats on scroll
    animateStatsOnScroll();
    
    function updateOnlineUsers() {
        const onlineElement = document.getElementById('online-users');
        if (!onlineElement) return;
        
        // Simulate real-time user count
        const baseUsers = 45;
        const variance = 15;
        const randomUsers = Math.floor(baseUsers + (Math.random() * variance * 2) - variance);
        
        onlineElement.textContent = randomUsers;
        
        // Update every 10-30 seconds with some randomness
        const nextUpdate = (Math.random() * 20000) + 10000;
        setTimeout(updateOnlineUsers, nextUpdate);
    }
    
    function updateLiveStats() {
        // This would normally fetch real data from your API
        const elements = {
            'total-views': document.getElementById('total-views'),
            'total-comments': document.getElementById('total-comments')
        };
        
        Object.keys(elements).forEach(key => {
            const element = elements[key];
            if (element) {
                const currentValue = parseInt(element.textContent.replace(/,/g, ''));
                const increment = Math.floor(Math.random() * 5) + 1;
                const newValue = currentValue + increment;
                
                animateNumber(element, currentValue, newValue);
            }
        });
    }
    
    function animateNumber(element, start, end) {
        const duration = 1000;
        const steps = 20;
        const stepValue = (end - start) / steps;
        const stepDuration = duration / steps;
        
        let current = start;
        const timer = setInterval(() => {
            current += stepValue;
            if (current >= end) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, stepDuration);
    }
    
    function setupNewsletterForm() {
        const newsletterForm = document.querySelector('.newsletter-form');
        if (!newsletterForm) return;
        
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
                submitBtn.textContent = '✓ Suscrito';
                submitBtn.style.background = '#28a745';
                
                // Reset form
                setTimeout(() => {
                    this.reset();
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    submitBtn.style.background = '';
                }, 2000);
                
                // Show success message
                showNotification('¡Suscripción exitosa! Revisa tu email.', 'success');
            }, 1500);
        });
    }
    
    function animateStatsOnScroll() {
        const statsWidget = document.querySelector('.widget-live-stats');
        if (!statsWidget) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-number');
                    
                    statNumbers.forEach(stat => {
                        const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
                        stat.textContent = '0';
                        animateNumber(stat, 0, finalValue);
                    });
                    
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(statsWidget);
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Popular videos hover effects
    const popularItems = document.querySelectorAll('.popular-video-item, .recent-video-item');
    popularItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Social links analytics tracking
    const socialLinks = document.querySelectorAll('.social-link');
    socialLinks.forEach(link => {
        link.addEventListener('click', function() {
            const platform = this.className.match(/social-(\w+)/)[1];
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'social_click', {
                    'platform': platform,
                    'location': 'sidebar'
                });
            }
        });
    });
    
    // Tags cloud dynamic sizing
    const tagItems = document.querySelectorAll('.tag-item');
    tagItems.forEach(tag => {
        tag.addEventListener('mouseenter', function() {
            const currentSize = parseInt(this.style.fontSize);
            this.style.fontSize = (currentSize + 2) + 'px';
        });
        
        tag.addEventListener('mouseleave', function() {
            const currentSize = parseInt(this.style.fontSize);
            this.style.fontSize = (currentSize - 2) + 'px';
        });
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>