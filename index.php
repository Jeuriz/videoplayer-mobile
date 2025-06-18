<?php
/**
 * Main template file
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <?php if (have_posts()) : ?>
        
        <?php if (is_home() && !is_front_page()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            </header>
        <?php endif; ?>

        <!-- Featured Video Section (only on home page) -->
        <?php if (is_home() || is_front_page()) : ?>
            <?php
            $featured_video = new WP_Query(array(
                'post_type' => 'video',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => '_featured_video',
                        'value' => '1',
                        'compare' => '='
                    )
                )
            ));
            
            if (!$featured_video->have_posts()) {
                // If no featured video, get the most recent video
                $featured_video = new WP_Query(array(
                    'post_type' => 'video',
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
            }
            ?>
            
            <?php if ($featured_video->have_posts()) : ?>
                <section class="featured-video-section">
                    <h2 class="section-title"><?php esc_html_e('Video Destacado', 'videoplayer'); ?></h2>
                    
                    <?php while ($featured_video->have_posts()) : $featured_video->the_post(); ?>
                        <article class="featured-video-card">
                            <div class="video-container">
                                <div class="video-player" onclick="location.href='<?php the_permalink(); ?>'">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('large', array('class' => 'featured-video-thumbnail')); ?>
                                    <?php else : ?>
                                        <div class="featured-video-placeholder">
                                            <span class="play-icon-large">▶</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="featured-video-overlay">
                                        <button class="play-btn-large">
                                            <div class="play-icon"></div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="featured-video-info">
                                <h3 class="featured-video-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="featured-video-meta">
                                    <span class="views">👁️ <?php echo number_format(get_video_views()); ?> visualizaciones</span>
                                    <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                    <span class="duration">⏱️ <?php echo esc_html(get_video_duration()); ?></span>
                                </div>
                                
                                <?php if (has_excerpt()) : ?>
                                    <div class="featured-video-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="watch-now-btn">
                                    <?php esc_html_e('Ver Ahora', 'videoplayer'); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search Form -->
        <?php if (is_home() || is_front_page()) : ?>
            <section class="search-section">
                <div class="search-container">
                    <?php get_search_form(); ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Videos Grid -->
        <section class="videos-section">
            <?php if (is_search()) : ?>
                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        printf(
                            esc_html__('Resultados de búsqueda para: %s', 'videoplayer'),
                            '<span>' . get_search_query() . '</span>'
                        );
                        ?>
                    </h1>
                </header>
            <?php elseif (is_category()) : ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_cat_title(); ?></h1>
                    <?php if (category_description()) : ?>
                        <div class="category-description"><?php echo category_description(); ?></div>
                    <?php endif; ?>
                </header>
            <?php elseif (is_tag()) : ?>
                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        printf(
                            esc_html__('Etiqueta: %s', 'videoplayer'),
                            single_tag_title('', false)
                        );
                        ?>
                    </h1>
                </header>
            <?php elseif (is_home() || is_front_page()) : ?>
                <h2 class="section-title"><?php esc_html_e('Últimos Videos', 'videoplayer'); ?></h2>
            <?php endif; ?>

            <div class="videos-grid">
                <?php
                $post_count = 0;
                while (have_posts()) : the_post();
                    $post_count++;
                    
                    // Skip featured video on home page
                    if ((is_home() || is_front_page()) && $post_count === 1 && get_post_type() === 'video' && get_post_meta(get_the_ID(), '_featured_video', true)) {
                        continue;
                    }
                ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('video-card fade-in'); ?>>
                        <div class="video-thumbnail">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                <?php else : ?>
                                    <div class="thumbnail-placeholder">
                                        <span class="play-icon">▶</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (get_post_type() === 'video') : ?>
                                    <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <div class="video-card-content">
                            <h3 class="video-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <div class="video-card-meta">
                                <?php if (get_post_type() === 'video') : ?>
                                    <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                                <?php endif; ?>
                                <span class="date"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                            </div>

                            <?php if (has_excerpt()) : ?>
                                <div class="video-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            $pagination = paginate_links(array(
                'type' => 'array',
                'prev_text' => '‹ ' . esc_html__('Anterior', 'videoplayer'),
                'next_text' => esc_html__('Siguiente', 'videoplayer') . ' ›',
            ));

            if ($pagination) :
            ?>
                <nav class="pagination" role="navigation" aria-label="<?php esc_attr_e('Paginación', 'videoplayer'); ?>">
                    <?php foreach ($pagination as $page) : ?>
                        <?php echo $page; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

        </section>

    <?php else : ?>
        
        <!-- No posts found -->
        <section class="no-results">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('No se encontraron resultados', 'videoplayer'); ?></h1>
            </header>

            <div class="page-content">
                <?php if (is_home() && current_user_can('publish_posts')) : ?>
                    <p>
                        <?php
                        printf(
                            wp_kses(
                                __('¿Listo para publicar tu primer post? <a href="%1$s">Comienza aquí</a>.', 'videoplayer'),
                                array(
                                    'a' => array(
                                        'href' => array(),
                                    ),
                                )
                            ),
                            esc_url(admin_url('post-new.php'))
                        );
                        ?>
                    </p>
                <?php elseif (is_search()) : ?>
                    <p><?php esc_html_e('Lo siento, pero no se encontraron resultados para tu búsqueda. Intenta con diferentes palabras clave.', 'videoplayer'); ?></p>
                    <?php get_search_form(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('No se encontró contenido. Intenta buscar o navegar por las categorías.', 'videoplayer'); ?></p>
                    <?php get_search_form(); ?>
                <?php endif; ?>
            </div>
        </section>

    <?php endif; ?>
</div>

<!-- Sidebar for widgets (only on larger screens) -->
<?php if (is_active_sidebar('homepage-widgets')) : ?>
    <aside class="homepage-sidebar">
        <?php dynamic_sidebar('homepage-widgets'); ?>
    </aside>
<?php endif; ?>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
}

.page-title {
    font-size: 2.5rem;
    margin-bottom: 10px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.category-description {
    color: var(--muted-text);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Featured Video Section */
.featured-video-section {
    margin-bottom: 50px;
}

.featured-video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
    margin-bottom: 30px;
}

.featured-video-card .video-container {
    position: relative;
    height: 300px;
    background: var(--darker-bg);
}

.featured-video-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-video-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
}

.play-icon-large {
    font-size: 60px;
    color: rgba(255, 255, 255, 0.8);
}

.featured-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.featured-video-overlay:hover {
    background: rgba(0, 0, 0, 0.5);
}

.play-btn-large {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow-light);
}

.play-btn-large:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 1);
}

.play-btn-large .play-icon {
    width: 0;
    height: 0;
    border-left: 25px solid #333;
    border-top: 15px solid transparent;
    border-bottom: 15px solid transparent;
    margin-left: 5px;
}

.featured-video-info {
    padding: 25px;
}

.featured-video-title {
    font-size: 24px;
    margin-bottom: 15px;
}

.featured-video-title a {
    color: var(--light-text);
    transition: var(--transition);
}

.featured-video-title a:hover {
    color: var(--primary-color);
}

.featured-video-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 14px;
    color: var(--muted-text);
}

.featured-video-excerpt {
    color: var(--muted-text);
    line-height: 1.6;
    margin-bottom: 20px;
}

.watch-now-btn {
    display: inline-block;
    background: var(--gradient-primary);
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.watch-now-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

/* Search Section */
.search-section {
    margin-bottom: 40px;
}

.search-container {
    max-width: 600px;
    margin: 0 auto;
}

/* Video Card Enhancements */
.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.thumbnail-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(45deg, #333, #555);
    display: flex;
    align-items: center;
    justify-content: center;
}

.thumbnail-placeholder .play-icon {
    font-size: 40px;
    color: rgba(255, 255, 255, 0.6);
}

.video-card-excerpt {
    color: var(--muted-text);
    font-size: 13px;
    line-height: 1.4;
    margin-top: 8px;
}

/* Homepage Sidebar */
.homepage-sidebar {
    display: none;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results .page-content {
    max-width: 600px;
    margin: 0 auto;
}

.no-results .page-content p {
    color: var(--muted-text);
    margin-bottom: 30px;
    font-size: 16px;
    line-height: 1.6;
}

/* Responsive Design */
@media (min-width: 768px) {
    .featured-video-card .video-container {
        height: 400px;
    }
    
    .featured-video-meta {
        justify-content: flex-start;
    }
    
    .page-title {
        font-size: 3rem;
    }
}

@media (min-width: 1024px) {
    .homepage-sidebar {
        display: block;
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 280px;
        z-index: 50;
    }
    
    .homepage-sidebar .widget {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
    }
    
    .homepage-sidebar .widget-title {
        color: var(--primary-color);
        font-size: 16px;
        margin-bottom: 15px;
    }
}

@media (max-width: 480px) {
    .featured-video-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .featured-video-title {
        font-size: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to video cards
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach((card, index) => {
        // Stagger animations
        card.style.animationDelay = (index * 0.1) + 's';
        
        // Add click analytics (if needed)
        card.addEventListener('click', function() {
            const videoTitle = this.querySelector('.video-card-title a')?.textContent;
            
            // Track video clicks (you can integrate with analytics)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'video_click', {
                    'video_title': videoTitle,
                    'page_location': window.location.href
                });
            }
        });
    });
    
    // Infinite scroll (optional)
    if (window.IntersectionObserver) {
        const loadMoreTrigger = document.querySelector('.pagination');
        
        if (loadMoreTrigger) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Implement infinite scroll logic here if needed
                        console.log('Load more videos trigger');
                    }
                });
            }, {
                rootMargin: '100px'
            });
            
            observer.observe(loadMoreTrigger);
        }
    }
});
</script>

<?php get_footer(); ?>