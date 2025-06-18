<?php
/**
 * Video Archive Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <div class="archive-header">
        <h1 class="archive-title">
            <?php 
            if (is_category()) {
                single_cat_title();
            } elseif (is_tag()) {
                single_tag_title();
            } elseif (is_tax()) {
                single_term_title();
            } else {
                esc_html_e('Videos', 'videoplayer');
            }
            ?>
        </h1>
        
        <?php if (is_category() || is_tag() || is_tax()) : ?>
            <div class="archive-description">
                <?php echo term_description(); ?>
            </div>
        <?php endif; ?>

        <!-- Video Stats -->
        <div class="archive-stats">
            <span class="total-videos">
                <?php 
                global $wp_query;
                printf(
                    esc_html(_n('%d video encontrado', '%d videos encontrados', $wp_query->found_posts, 'videoplayer')),
                    number_format_i18n($wp_query->found_posts)
                );
                ?>
            </span>
        </div>
    </div>

    <!-- Filter and Sort Controls -->
    <div class="video-controls">
        <div class="view-controls">
            <button class="view-toggle active" data-view="grid" aria-label="<?php esc_attr_e('Vista de cuadrícula', 'videoplayer'); ?>">
                <span class="icon">⊞</span>
            </button>
            <button class="view-toggle" data-view="list" aria-label="<?php esc_attr_e('Vista de lista', 'videoplayer'); ?>">
                <span class="icon">☰</span>
            </button>
        </div>

        <div class="filter-controls">
            <!-- Category Filter -->
            <div class="filter-group">
                <label for="category-filter"><?php esc_html_e('Categoría:', 'videoplayer'); ?></label>
                <select id="category-filter" class="filter-select">
                    <option value=""><?php esc_html_e('Todas las categorías', 'videoplayer'); ?></option>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'category',
                        'hide_empty' => true,
                    ));
                    
                    foreach ($categories as $category) {
                        $selected = (is_category($category->term_id)) ? 'selected' : '';
                        echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>';
                        echo esc_html($category->name) . ' (' . $category->count . ')';
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Sort Options -->
            <div class="filter-group">
                <label for="sort-filter"><?php esc_html_e('Ordenar por:', 'videoplayer'); ?></label>
                <select id="sort-filter" class="filter-select">
                    <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>><?php esc_html_e('Más recientes', 'videoplayer'); ?></option>
                    <option value="popular" <?php selected(get_query_var('orderby'), 'popular'); ?>><?php esc_html_e('Más populares', 'videoplayer'); ?></option>
                    <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>><?php esc_html_e('Título A-Z', 'videoplayer'); ?></option>
                    <option value="duration" <?php selected(get_query_var('orderby'), 'duration'); ?>><?php esc_html_e('Duración', 'videoplayer'); ?></option>
                </select>
            </div>

            <!-- Duration Filter -->
            <div class="filter-group">
                <label for="duration-filter"><?php esc_html_e('Duración:', 'videoplayer'); ?></label>
                <select id="duration-filter" class="filter-select">
                    <option value=""><?php esc_html_e('Cualquier duración', 'videoplayer'); ?></option>
                    <option value="short"><?php esc_html_e('Corto (< 5 min)', 'videoplayer'); ?></option>
                    <option value="medium"><?php esc_html_e('Medio (5-15 min)', 'videoplayer'); ?></option>
                    <option value="long"><?php esc_html_e('Largo (> 15 min)', 'videoplayer'); ?></option>
                </select>
            </div>
        </div>

        <!-- Search in Videos -->
        <div class="video-search">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" 
                       class="search-field" 
                       placeholder="<?php esc_attr_e('Buscar videos...', 'videoplayer'); ?>" 
                       value="<?php echo get_search_query(); ?>" 
                       name="s">
                <input type="hidden" name="post_type" value="video">
                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Buscar', 'videoplayer'); ?>">
                    <span class="icon">🔍</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Featured Videos Section -->
    <?php if (is_home() || is_post_type_archive('video')) : ?>
        <?php
        $featured_videos = get_featured_videos(3);
        if ($featured_videos->have_posts()) :
        ?>
            <section class="featured-videos-section">
                <h2 class="section-title"><?php esc_html_e('Videos Destacados', 'videoplayer'); ?></h2>
                
                <div class="featured-videos-grid">
                    <?php while ($featured_videos->have_posts()) : $featured_videos->the_post(); ?>
                        <article class="featured-video-card">
                            <div class="featured-video-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('video-large'); ?>
                                    <?php else : ?>
                                        <div class="thumbnail-placeholder">
                                            <span class="video-icon">▶</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="video-overlay">
                                        <div class="play-btn">
                                            <div class="play-icon"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="video-badges">
                                        <span class="featured-badge"><?php esc_html_e('Destacado', 'videoplayer'); ?></span>
                                    </div>
                                    
                                    <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                </a>
                            </div>
                            
                            <div class="featured-video-content">
                                <h3 class="featured-video-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="featured-video-meta">
                                    <span class="views">👁️ <?php echo number_format(get_video_views()); ?></span>
                                    <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                </div>
                                
                                <div class="featured-video-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Videos Grid/List -->
    <main class="videos-main">
        <?php if (have_posts()) : ?>
            
            <div class="videos-container" id="videos-grid" data-view="grid">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('video-card'); ?>>
                        <div class="video-thumbnail">
                            <a href="<?php the_permalink(); ?>" onclick="handleVideoClick(event)">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('video-thumbnail'); ?>
                                <?php else : ?>
                                    <div class="thumbnail-placeholder">
                                        <span class="video-icon">▶</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="video-overlay">
                                    <div class="play-btn">
                                        <div class="play-icon"></div>
                                    </div>
                                </div>
                                
                                <?php if (get_theme_mod('show_video_duration', true)) : ?>
                                    <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                <?php endif; ?>
                                
                                <?php if (is_featured_video()) : ?>
                                    <div class="video-badges">
                                        <span class="featured-badge"><?php esc_html_e('Destacado', 'videoplayer'); ?></span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        
                        <div class="video-content">
                            <h3 class="video-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="video-meta">
                                <?php if (get_theme_mod('show_view_count', true)) : ?>
                                    <span class="views">👁️ <?php echo number_format(get_video_views()); ?></span>
                                <?php endif; ?>
                                <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                <span class="author">👤 <?php the_author(); ?></span>
                            </div>
                            
                            <div class="video-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </div>
                            
                            <?php if (has_category()) : ?>
                                <div class="video-categories">
                                    <?php the_category(' '); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                    
                <?php endwhile; ?>
            </div>

            <!-- Load More Button -->
            <?php if (get_next_posts_link()) : ?>
                <div class="load-more-container">
                    <button id="load-more-videos" class="load-more-btn" 
                            data-page="2" 
                            data-max-pages="<?php echo $wp_query->max_num_pages; ?>">
                        <span class="text"><?php esc_html_e('Cargar más videos', 'videoplayer'); ?></span>
                        <span class="loading hidden">⏳ <?php esc_html_e('Cargando...', 'videoplayer'); ?></span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <nav class="navigation pagination" role="navigation" aria-label="<?php esc_attr_e('Navegación de videos', 'videoplayer'); ?>">
                <?php
                echo paginate_links(array(
                    'prev_text' => '← ' . __('Anterior', 'videoplayer'),
                    'next_text' => __('Siguiente', 'videoplayer') . ' →',
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Página', 'videoplayer') . ' </span>',
                ));
                ?>
            </nav>

        <?php else : ?>
            
            <div class="no-videos-found">
                <div class="no-videos-icon">📹</div>
                <h2><?php esc_html_e('No se encontraron videos', 'videoplayer'); ?></h2>
                <p><?php esc_html_e('No hay videos que coincidan con tu búsqueda. Intenta con otros términos o explora nuestras categorías.', 'videoplayer'); ?></p>
                
                <div class="no-videos-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="btn btn-primary">
                        <?php esc_html_e('Ver todos los videos', 'videoplayer'); ?>
                    </a>
                    
                    <?php if (is_search()) : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-secondary">
                            <?php esc_html_e('Ir al inicio', 'videoplayer'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php endif; ?>
    </main>
</div>

<!-- Sidebar -->
<?php if (is_active_sidebar('video-archive-sidebar')) : ?>
    <aside class="archive-sidebar">
        <?php dynamic_sidebar('video-archive-sidebar'); ?>
    </aside>
<?php endif; ?>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.archive-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
    border-bottom: 1px solid var(--border-color);
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
    font-size: 1.1rem;
    color: var(--muted-text);
    max-width: 600px;
    margin: 0 auto 20px;
    line-height: 1.6;
}

.archive-stats {
    color: var(--muted-text);
    font-size: 14px;
}

.video-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.view-controls {
    display: flex;
    gap: 5px;
}

.view-toggle {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 10px 15px;
    cursor: pointer;
    transition: var(--transition);
    border-radius: 6px;
}

.view-toggle:hover,
.view-toggle.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.filter-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group label {
    font-size: 14px;
    color: var(--muted-text);
    white-space: nowrap;
}

.filter-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 8px 12px;
    border-radius: 6px;
    min-width: 150px;
}

.video-search {
    display: flex;
}

.search-form {
    display: flex;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 25px;
    overflow: hidden;
}

.search-field {
    background: none;
    border: none;
    color: var(--light-text);
    padding: 10px 15px;
    min-width: 200px;
    outline: none;
}

.search-field::placeholder {
    color: var(--muted-text);
}

.search-submit {
    background: var(--primary-color);
    border: none;
    color: white;
    padding: 10px 15px;
    cursor: pointer;
    transition: var(--transition);
}

.search-submit:hover {
    background: var(--secondary-color);
}

.featured-videos-section {
    margin-bottom: 50px;
}

.section-title {
    font-size: 2rem;
    margin-bottom: 30px;
    text-align: center;
    color: var(--primary-color);
}

.featured-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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

.featured-video-thumbnail {
    position: relative;
    height: 200px;
    background: var(--darker-bg);
}

.featured-video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.videos-container {
    display: grid;
    gap: 25px;
    margin-bottom: 40px;
}

.videos-container[data-view="grid"] {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}

.videos-container[data-view="list"] {
    grid-template-columns: 1fr;
}

.videos-container[data-view="list"] .video-card {
    display: flex;
    gap: 20px;
}

.videos-container[data-view="list"] .video-thumbnail {
    flex: 0 0 200px;
    height: 120px;
}

.videos-container[data-view="list"] .video-content {
    flex: 1;
}

.video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.video-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.08);
}

.video-thumbnail {
    position: relative;
    height: 160px;
    background: var(--darker-bg);
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.thumbnail-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
    color: rgba(255, 255, 255, 0.6);
}

.video-icon {
    font-size: 3rem;
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

.video-card:hover .video-overlay {
    opacity: 1;
}

.play-btn {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(0.8);
    transition: var(--transition);
}

.video-card:hover .play-btn {
    transform: scale(1);
    background: var(--primary-color);
}

.play-icon {
    width: 0;
    height: 0;
    border-left: 15px solid var(--dark-bg);
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    margin-left: 3px;
}

.video-card:hover .play-icon {
    border-left-color: white;
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.video-badges {
    position: absolute;
    top: 10px;
    left: 10px;
}

.featured-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.video-content {
    padding: 20px;
}

.video-title {
    font-size: 16px;
    margin-bottom: 10px;
    line-height: 1.3;
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
    flex-wrap: wrap;
    gap: 15px;
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 10px;
}

.video-excerpt {
    color: var(--muted-text);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 10px;
}

.video-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.video-categories a {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    text-decoration: none;
    transition: var(--transition);
}

.video-categories a:hover {
    background: var(--primary-color);
    color: white;
}

.load-more-container {
    text-align: center;
    margin: 40px 0;
}

.load-more-btn {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: var(--transition);
}

.load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.load-more-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.hidden {
    display: none;
}

.navigation.pagination {
    text-align: center;
    margin: 40px 0;
}

.navigation .nav-links {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.navigation .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    text-decoration: none;
    border-radius: 6px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.navigation .page-numbers:hover,
.navigation .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.no-videos-found {
    text-align: center;
    padding: 80px 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.no-videos-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-videos-found h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.no-videos-found p {
    font-size: 1.1rem;
    color: var(--muted-text);
    max-width: 500px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.no-videos-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: 1px solid transparent;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    border-color: var(--border-color);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

@media (max-width: 768px) {
    .video-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-controls {
        width: 100%;
        justify-content: center;
    }
    
    .filter-group {
        flex-direction: column;
        gap: 5px;
    }
    
    .videos-container[data-view="grid"] {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .videos-container[data-view="list"] .video-card {
        flex-direction: column;
    }
    
    .videos-container[data-view="list"] .video-thumbnail {
        flex: none;
        height: 160px;
    }
    
    .featured-videos-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .videos-container[data-view="grid"] {
        grid-template-columns: 1fr;
    }
    
    .search-field {
        min-width: 150px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initVideoArchive();
});

function initVideoArchive() {
    // View toggle functionality
    const viewToggles = document.querySelectorAll('.view-toggle');
    const videosContainer = document.getElementById('videos-grid');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active state
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update container view
            videosContainer.dataset.view = view;
            
            // Save preference
            localStorage.setItem('videoArchiveView', view);
        });
    });
    
    // Load saved view preference
    const savedView = localStorage.getItem('videoArchiveView');
    if (savedView) {
        const targetToggle = document.querySelector(`[data-view="${savedView}"]`);
        if (targetToggle) {
            targetToggle.click();
        }
    }
    
    // Filter functionality
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
    
    // Load more functionality
    const loadMoreBtn = document.getElementById('load-more-videos');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreVideos);
    }
}

function applyFilters() {
    const category = document.getElementById('category-filter').value;
    const sort = document.getElementById('sort-filter').value;
    const duration = document.getElementById('duration-filter').value;
    
    // Build URL with filters
    const url = new URL(window.location);
    
    if (category) url.searchParams.set('category', category);
    else url.searchParams.delete('category');
    
    if (sort && sort !== 'date') url.searchParams.set('orderby', sort);
    else url.searchParams.delete('orderby');
    
    if (duration) url.searchParams.set('duration', duration);
    else url.searchParams.delete('duration');
    
    // Reload page with new filters
    window.location.href = url.toString();
}

function loadMoreVideos() {
    const btn = document.getElementById('load-more-videos');
    const currentPage = parseInt(btn.dataset.page);
    const maxPages = parseInt(btn.dataset.maxPages);
    
    if (currentPage >= maxPages) {
        btn.style.display = 'none';
        return;
    }
    
    // Show loading state
    btn.querySelector('.text').classList.add('hidden');
    btn.querySelector('.loading').classList.remove('hidden');
    btn.disabled = true;
    
    // AJAX request
    const formData = new FormData();
    formData.append('action', 'load_more_videos');
    formData.append('page', currentPage);
    formData.append('posts_per_page', 12);
    formData.append('nonce', videoPlayerAjax.nonce);
    
    fetch(videoPlayerAjax.ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Append new videos
            const videosContainer = document.getElementById('videos-grid');
            videosContainer.insertAdjacentHTML('beforeend', data.data.html);
            
            // Update button state
            btn.dataset.page = currentPage + 1;
            
            if (currentPage + 1 >= maxPages) {
                btn.style.display = 'none';
            }
        } else {
            console.error('Error loading more videos:', data.data);
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
    })
    .finally(() => {
        // Reset button state
        btn.querySelector('.text').classList.remove('hidden');
        btn.querySelector('.loading').classList.add('hidden');
        btn.disabled = false;
    });
}

function handleVideoClick(event) {
    // Use the global function from main.js
    if (window.VideoPlayerTheme) {
        window.VideoPlayerTheme.handleVideoClick(event);
    }
}
</script>

<?php get_footer(); ?>