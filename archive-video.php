<?php
/**
 * Archive template for videos
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <header class="archive-header">
        <h1 class="archive-title">
            <?php
            if (is_category()) {
                single_cat_title();
            } elseif (is_tag()) {
                printf(esc_html__('Etiqueta: %s', 'videoplayer'), single_tag_title('', false));
            } elseif (isset($_GET['orderby']) && $_GET['orderby'] === 'popular') {
                esc_html_e('Videos Populares', 'videoplayer');
            } else {
                esc_html_e('Todos los Videos', 'videoplayer');
            }
            ?>
        </h1>
        
        <?php if (is_category() && category_description()) : ?>
            <div class="archive-description">
                <?php echo category_description(); ?>
            </div>
        <?php endif; ?>
        
        <div class="archive-stats">
            <?php
            global $wp_query;
            $total_videos = $wp_query->found_posts;
            printf(
                esc_html(_n('%d video encontrado', '%d videos encontrados', $total_videos, 'videoplayer')),
                number_format_i18n($total_videos)
            );
            ?>
        </div>
    </header>

    <!-- Filter and Sort Options -->
    <div class="video-filters">
        <div class="filter-group">
            <label for="sort-select" class="filter-label"><?php esc_html_e('Ordenar por:', 'videoplayer'); ?></label>
            <select id="sort-select" class="filter-select">
                <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>>
                    <?php esc_html_e('Más recientes', 'videoplayer'); ?>
                </option>
                <option value="popular" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'popular'); ?>>
                    <?php esc_html_e('Más populares', 'videoplayer'); ?>
                </option>
                <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>
                    <?php esc_html_e('Alfabético', 'videoplayer'); ?>
                </option>
                <option value="duration" <?php selected(get_query_var('orderby'), 'duration'); ?>>
                    <?php esc_html_e('Duración', 'videoplayer'); ?>
                </option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="view-toggle" class="filter-label"><?php esc_html_e('Vista:', 'videoplayer'); ?></label>
            <div class="view-toggle" id="view-toggle">
                <button class="view-btn active" data-view="grid" aria-label="<?php esc_attr_e('Vista en grilla', 'videoplayer'); ?>">⊞</button>
                <button class="view-btn" data-view="list" aria-label="<?php esc_attr_e('Vista en lista', 'videoplayer'); ?>">☰</button>
            </div>
        </div>
        
        <div class="search-filter">
            <?php get_search_form(); ?>
        </div>
    </div>

    <?php if (have_posts()) : ?>
        
        <div class="videos-container">
            <div class="videos-grid" id="videos-container">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('video-card fade-in'); ?>>
                        <div class="video-thumbnail">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', array('loading' => 'lazy')); ?>
                                <?php else : ?>
                                    <div class="thumbnail-placeholder">
                                        <span class="play-icon">▶</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="video-overlay">
                                    <div class="play-btn">
                                        <div class="play-icon"></div>
                                    </div>
                                </div>
                                
                                <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                
                                <?php if (is_redirect_enabled()) : ?>
                                    <div class="redirect-badge" title="<?php esc_attr_e('Video con redirección', 'videoplayer'); ?>">🔗</div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <div class="video-card-content">
                            <h3 class="video-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <div class="video-card-meta">
                                <div class="meta-primary">
                                    <span class="views">👁️ <?php echo number_format(get_video_views()); ?></span>
                                    <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                </div>
                                
                                <?php if (has_category()) : ?>
                                    <div class="meta-categories">
                                        <?php the_category(' '); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (has_excerpt()) : ?>
                                <div class="video-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="video-card-actions">
                                <a href="<?php the_permalink(); ?>" class="watch-btn">
                                    <?php esc_html_e('Ver Video', 'videoplayer'); ?>
                                </a>
                                
                                <button class="quick-share-btn" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>">
                                    📤
                                </button>
                            </div>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>
        </div>

        <!-- Load More Button -->
        <?php if (get_next_posts_link()) : ?>
            <div class="load-more-container">
                <button id="load-more-btn" class="load-more-btn" data-page="1" data-max="<?php echo $wp_query->max_num_pages; ?>">
                    <span class="btn-text"><?php esc_html_e('Cargar Más Videos', 'videoplayer'); ?></span>
                    <span class="btn-loading" style="display: none;">⟳ <?php esc_html_e('Cargando...', 'videoplayer'); ?></span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Traditional Pagination (fallback) -->
        <nav class="pagination-nav" role="navigation" aria-label="<?php esc_attr_e('Navegación de videos', 'videoplayer'); ?>">
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '‹ ' . esc_html__('Anterior', 'videoplayer'),
                'next_text' => esc_html__('Siguiente', 'videoplayer') . ' ›',
            ));
            ?>
        </nav>

    <?php else : ?>
        
        <!-- No videos found -->
        <div class="no-videos">
            <div class="no-videos-icon">📹</div>
            <h2><?php esc_html_e('No se encontraron videos', 'videoplayer'); ?></h2>
            <p><?php esc_html_e('No hay videos que coincidan con tus criterios de búsqueda.', 'videoplayer'); ?></p>
            
            <div class="no-videos-actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="btn-primary">
                    <?php esc_html_e('Ver Todos los Videos', 'videoplayer'); ?>
                </a>
                
                <?php get_search_form(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Popular Videos Sidebar -->
<?php
$popular_videos = new WP_Query(array(
    'post_type' => 'video',
    'posts_per_page' => 5,
    'meta_key' => '_view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post__not_in' => wp_list_pluck($GLOBALS['wp_query']->posts, 'ID')
));
?>

<?php if ($popular_videos->have_posts()) : ?>
    <aside class="popular-sidebar">
        <h3 class="sidebar-title"><?php esc_html_e('Videos Populares', 'videoplayer'); ?></h3>
        
        <div class="popular-videos-list">
            <?php while ($popular_videos->have_posts()) : $popular_videos->the_post(); ?>
                <div class="popular-video-item">
                    <div class="popular-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                <div class="thumbnail-placeholder-small">▶</div>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="popular-info">
                        <h4 class="popular-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="popular-meta">
                            <?php echo number_format(get_video_views()); ?> vistas
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </aside>
<?php endif; ?>

<style>
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.archive-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
}

.archive-title {
    font-size: 2.5rem;
    margin-bottom: 15px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.archive-description {
    color: var(--muted-text);
    max-width: 600px;
    margin: 0 auto 20px;
    line-height: 1.6;
}

.archive-stats {
    color: var(--muted-text);
    font-size: 14px;
}

/* Filters */
.video-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-label {
    font-size: 14px;
    color: var(--muted-text);
    white-space: nowrap;
}

.filter-select {
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.view-toggle {
    display: flex;
    background: var(--hover-bg);
    border-radius: 6px;
    overflow: hidden;
}

.view-btn {
    background: none;
    border: none;
    color: var(--muted-text);
    padding: 8px 12px;
    cursor: pointer;
    transition: var(--transition);
}

.view-btn.active,
.view-btn:hover {
    background: var(--primary-color);
    color: white;
}

.search-filter {
    margin-left: auto;
}

/* Video Grid Enhancements */
.videos-container {
    position: relative;
}

.videos-grid.list-view .video-card {
    display: flex;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    overflow: hidden;
}

.videos-grid.list-view .video-thumbnail {
    width: 300px;
    flex-shrink: 0;
}

.videos-grid.list-view .video-card-content {
    flex: 1;
    padding: 20px;
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

.video-overlay .play-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.video-overlay .play-btn:hover {
    transform: scale(1.1);
    background: white;
}

.video-overlay .play-icon {
    width: 0;
    height: 0;
    border-left: 15px solid #333;
    border-top: 9px solid transparent;
    border-bottom: 9px solid transparent;
    margin-left: 3px;
}

.redirect-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.meta-primary {
    display: flex;
    gap: 15px;
    margin-bottom: 8px;
}

.meta-categories {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.meta-categories a {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
    transition: var(--transition);
}

.meta-categories a:hover {
    background: var(--primary-color);
    color: white;
}

.video-card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.watch-btn {
    background: var(--gradient-primary);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: var(--transition);
}

.watch-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.quick-share-btn {
    background: none;
    border: none;
    color: var(--muted-text);
    font-size: 16px;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    transition: var(--transition);
}

.quick-share-btn:hover {
    background: var(--hover-bg);
    color: var(--primary-color);
}

/* Load More */
.load-more-container {
    text-align: center;
    margin: 40px 0;
}

.load-more-btn {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.load-more-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.load-more-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* No Videos */
.no-videos {
    text-align: center;
    padding: 80px 20px;
    max-width: 500px;
    margin: 0 auto;
}

.no-videos-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-videos h2 {
    margin-bottom: 15px;
    color: var(--muted-text);
}

.no-videos p {
    color: var(--muted-text);
    margin-bottom: 30px;
    line-height: 1.6;
}

.no-videos-actions {
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

/* Popular Sidebar */
.popular-sidebar {
    display: none;
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 280px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 20px;
    backdrop-filter: blur(10px);
    z-index: 50;
    max-height: 80vh;
    overflow-y: auto;
}

.sidebar-title {
    color: var(--primary-color);
    font-size: 18px;
    margin-bottom: 20px;
    text-align: center;
}

.popular-video-item {
    display: flex;
    gap: 12px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.popular-video-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.popular-thumbnail {
    width: 60px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 6px;
    overflow: hidden;
}

.popular-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-placeholder-small {
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #333, #555);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.6);
    font-size: 12px;
}

.popular-info {
    flex: 1;
    min-width: 0;
}

.popular-title {
    font-size: 14px;
    margin-bottom: 5px;
}

.popular-title a {
    color: var(--light-text);
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

.popular-title a:hover {
    color: var(--primary-color);
}

.popular-meta {
    font-size: 12px;
    color: var(--muted-text);
}

/* Responsive */
@media (min-width: 768px) {
    .video-filters {
        justify-content: space-between;
        align-items: center;
    }
    
    .search-filter {
        margin-left: 0;
    }
}

@media (min-width: 1024px) {
    .popular-sidebar {
        display: block;
    }
}

@media (max-width: 768px) {
    .video-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        justify-content: space-between;
    }
    
    .videos-grid.list-view .video-card {
        flex-direction: column;
    }
    
    .videos-grid.list-view .video-thumbnail {
        width: 100%;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .video-card-actions {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sort functionality
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('orderby', this.value);
            currentUrl.searchParams.delete('paged');
            window.location.href = currentUrl.toString();
        });
    }
    
    // View toggle functionality
    const viewButtons = document.querySelectorAll('.view-btn');
    const videosContainer = document.getElementById('videos-container');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (view === 'list') {
                videosContainer.classList.add('list-view');
            } else {
                videosContainer.classList.remove('list-view');
            }
            
            // Save preference
            localStorage.setItem('videoView', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('videoView');
    if (savedView === 'list') {
        document.querySelector('[data-view="list"]')?.click();
    }
    
    // Load more functionality
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const btn = this;
            const currentPage = parseInt(btn.dataset.page);
            const maxPages = parseInt(btn.dataset.max);
            const nextPage = currentPage + 1;
            
            if (nextPage > maxPages) return;
            
            // Show loading state
            btn.disabled = true;
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loading').style.display = 'inline';
            
            // Build AJAX URL
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('paged', nextPage);
            
            fetch(currentUrl.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newVideos = doc.querySelectorAll('.video-card');
                    
                    if (newVideos.length > 0) {
                        newVideos.forEach((video, index) => {
                            video.style.animationDelay = (index * 0.1) + 's';
                            videosContainer.appendChild(video);
                        });
                        
                        btn.dataset.page = nextPage;
                        
                        if (nextPage >= maxPages) {
                            btn.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more videos:', error);
                })
                .finally(() => {
                    // Hide loading state
                    btn.disabled = false;
                    btn.querySelector('.btn-text').style.display = 'inline';
                    btn.querySelector('.btn-loading').style.display = 'none';
                });
        });
    }
    
    // Quick share functionality
    const shareButtons = document.querySelectorAll('.quick-share-btn');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const title = this.dataset.title;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).catch(console.error);
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(url).then(() => {
                    // Show temporary feedback
                    const originalText = this.innerHTML;
                    this.innerHTML = '✓';
                    this.style.color = 'var(--primary-color)';
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.color = '';
                    }, 1000);
                });
            }
        });
    });
    
    // Animation delays for video cards
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.05) + 's';
    });
});
</script>

<?php get_footer(); ?>