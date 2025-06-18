<?php
/**
 * Search results template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <header class="search-header">
        <h1 class="search-title">
            <?php
            printf(
                esc_html__('Resultados de búsqueda para: %s', 'videoplayer'),
                '<span class="search-query">' . get_search_query() . '</span>'
            );
            ?>
        </h1>
        
        <div class="search-stats">
            <?php
            global $wp_query;
            $total_results = $wp_query->found_posts;
            
            if ($total_results > 0) {
                printf(
                    esc_html(_n('Se encontró %d resultado', 'Se encontraron %d resultados', $total_results, 'videoplayer')),
                    number_format_i18n($total_results)
                );
                
                if ($wp_query->query_vars['paged'] > 1) {
                    printf(
                        esc_html__(' - Página %d de %d', 'videoplayer'),
                        $wp_query->query_vars['paged'],
                        $wp_query->max_num_pages
                    );
                }
            } else {
                esc_html_e('No se encontraron resultados', 'videoplayer');
            }
            ?>
        </div>
    </header>

    <!-- Search Form -->
    <div class="search-form-container">
        <?php get_search_form(); ?>
    </div>

    <?php if (have_posts()) : ?>
        
        <!-- Filter Options -->
        <div class="search-filters">
            <div class="filter-group">
                <label for="search-sort" class="filter-label"><?php esc_html_e('Ordenar por:', 'videoplayer'); ?></label>
                <select id="search-sort" class="filter-select">
                    <option value="relevance" <?php selected(get_query_var('orderby'), 'relevance'); ?>>
                        <?php esc_html_e('Relevancia', 'videoplayer'); ?>
                    </option>
                    <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>>
                        <?php esc_html_e('Más recientes', 'videoplayer'); ?>
                    </option>
                    <option value="popular" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'popular'); ?>>
                        <?php esc_html_e('Más populares', 'videoplayer'); ?>
                    </option>
                    <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>
                        <?php esc_html_e('Alfabético', 'videoplayer'); ?>
                    </option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="content-type" class="filter-label"><?php esc_html_e('Tipo:', 'videoplayer'); ?></label>
                <select id="content-type" class="filter-select">
                    <option value="all" <?php selected(get_query_var('post_type'), ''); ?>>
                        <?php esc_html_e('Todo el contenido', 'videoplayer'); ?>
                    </option>
                    <option value="video" <?php selected(get_query_var('post_type'), 'video'); ?>>
                        <?php esc_html_e('Solo videos', 'videoplayer'); ?>
                    </option>
                    <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>>
                        <?php esc_html_e('Solo artículos', 'videoplayer'); ?>
                    </option>
                </select>
            </div>
            
            <div class="results-view-toggle">
                <button class="view-btn active" data-view="grid" aria-label="<?php esc_attr_e('Vista en grilla', 'videoplayer'); ?>">⊞</button>
                <button class="view-btn" data-view="list" aria-label="<?php esc_attr_e('Vista en lista', 'videoplayer'); ?>">☰</button>
            </div>
        </div>

        <!-- Search Results -->
        <div class="search-results">
            <div class="results-grid" id="results-container">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('result-card fade-in'); ?>>
                        <?php if (get_post_type() === 'video') : ?>
                            <!-- Video Result -->
                            <div class="result-thumbnail video-thumbnail">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
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
                                    <div class="content-type-badge video-badge">📹 Video</div>
                                </a>
                            </div>
                            
                        <?php else : ?>
                            <!-- Post/Article Result -->
                            <div class="result-thumbnail article-thumbnail">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                    <?php else : ?>
                                        <div class="thumbnail-placeholder">
                                            <span class="article-icon">📄</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="content-type-badge article-badge">📄 Artículo</div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="result-content">
                            <h3 class="result-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    // Highlight search terms in title
                                    $title = get_the_title();
                                    $search_query = get_search_query();
                                    if ($search_query) {
                                        $title = preg_replace('/(' . preg_quote($search_query, '/') . ')/i', '<mark>$1</mark>', $title);
                                    }
                                    echo $title;
                                    ?>
                                </a>
                            </h3>

                            <div class="result-meta">
                                <div class="meta-primary">
                                    <?php if (get_post_type() === 'video') : ?>
                                        <span class="views">👁️ <?php echo number_format(get_video_views()); ?> vistas</span>
                                    <?php endif; ?>
                                    <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                    <span class="author">👤 <?php the_author(); ?></span>
                                </div>
                                
                                <?php if (has_category()) : ?>
                                    <div class="meta-categories">
                                        <?php the_category(' '); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="result-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt();
                                $search_query = get_search_query();
                                
                                if ($search_query && $excerpt) {
                                    // Highlight search terms in excerpt
                                    $excerpt = preg_replace('/(' . preg_quote($search_query, '/') . ')/i', '<mark>$1</mark>', $excerpt);
                                }
                                
                                echo wp_trim_words($excerpt, 25, '...');
                                ?>
                            </div>
                            
                            <div class="result-actions">
                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                    <?php echo (get_post_type() === 'video') ? esc_html__('Ver Video', 'videoplayer') : esc_html__('Leer Más', 'videoplayer'); ?>
                                </a>
                                
                                <div class="result-tags">
                                    <?php 
                                    $tags = get_the_tags();
                                    if ($tags && count($tags) > 0) {
                                        $tag_count = 0;
                                        foreach ($tags as $tag) {
                                            if ($tag_count >= 3) break;
                                            echo '<span class="result-tag">#' . esc_html($tag->name) . '</span>';
                                            $tag_count++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="search-pagination" role="navigation" aria-label="<?php esc_attr_e('Navegación de resultados', 'videoplayer'); ?>">
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '‹ ' . esc_html__('Anterior', 'videoplayer'),
                'next_text' => esc_html__('Siguiente', 'videoplayer') . ' ›',
            ));
            ?>
        </nav>

    <?php else : ?>
        
        <!-- No results -->
        <div class="no-search-results">
            <div class="no-results-icon">🔍</div>
            <h2><?php esc_html_e('No se encontraron resultados', 'videoplayer'); ?></h2>
            <p><?php esc_html_e('No pudimos encontrar contenido que coincida con tu búsqueda.', 'videoplayer'); ?></p>
            
            <div class="search-suggestions-container">
                <h3><?php esc_html_e('Sugerencias para mejorar tu búsqueda:', 'videoplayer'); ?></h3>
                <ul class="search-tips">
                    <li><?php esc_html_e('Verifica la ortografía de las palabras clave', 'videoplayer'); ?></li>
                    <li><?php esc_html_e('Intenta con términos más generales', 'videoplayer'); ?></li>
                    <li><?php esc_html_e('Usa menos palabras clave', 'videoplayer'); ?></li>
                    <li><?php esc_html_e('Prueba con sinónimos o términos relacionados', 'videoplayer'); ?></li>
                </ul>
            </div>
            
            <!-- Alternative searches -->
            <div class="alternative-searches">
                <h3><?php esc_html_e('Búsquedas populares:', 'videoplayer'); ?></h3>
                <div class="popular-searches-tags">
                    <?php 
                    $popular_searches = array('tutorial', 'diseño web', 'javascript', 'wordpress', 'css', 'responsive');
                    foreach ($popular_searches as $search) {
                        echo '<a href="' . esc_url(home_url('/?s=' . urlencode($search) . '&post_type=video')) . '" class="popular-search-tag">' . esc_html($search) . '</a>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Browse categories -->
            <div class="browse-categories">
                <h3><?php esc_html_e('O explora por categorías:', 'videoplayer'); ?></h3>
                <div class="categories-grid">
                    <?php 
                    $categories = get_categories(array('number' => 6, 'hide_empty' => true));
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-card">';
                        echo '<span class="category-name">' . esc_html($category->name) . '</span>';
                        echo '<span class="category-count">' . $category->count . ' videos</span>';
                        echo '</a>';
                    }
                    ?>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.search-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 30px 0;
}

.search-title {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.search-query {
    color: var(--primary-color);
    font-weight: 700;
}

.search-stats {
    color: var(--muted-text);
    font-size: 14px;
}

.search-form-container {
    max-width: 600px;
    margin: 0 auto 40px;
}

/* Search Filters */
.search-filters {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
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

.results-view-toggle {
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

/* Results Grid */
.results-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 25px;
    margin-bottom: 40px;
}

.results-grid.list-view .result-card {
    display: flex;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.results-grid.list-view .result-thumbnail {
    width: 250px;
    flex-shrink: 0;
}

.results-grid.list-view .result-content {
    flex: 1;
    padding: 20px;
}

.result-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.result-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: var(--shadow-light);
}

.result-thumbnail {
    position: relative;
    height: 200px;
    background: var(--darker-bg);
    overflow: hidden;
}

.result-thumbnail img {
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
    font-size: 40px;
    color: rgba(255, 255, 255, 0.6);
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

.result-thumbnail:hover .video-overlay {
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

.content-type-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.video-badge {
    background: var(--primary-color);
    color: white;
}

.article-badge {
    background: var(--secondary-color);
    color: white;
}

.result-content {
    padding: 20px;
}

.result-title {
    font-size: 18px;
    margin-bottom: 12px;
    line-height: 1.3;
}

.result-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.result-title a:hover {
    color: var(--primary-color);
}

.result-title mark {
    background: rgba(255, 107, 107, 0.3);
    color: var(--primary-color);
    padding: 2px 4px;
    border-radius: 3px;
}

.result-meta {
    margin-bottom: 15px;
}

.meta-primary {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 8px;
    font-size: 13px;
    color: var(--muted-text);
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
    font-size: 11px;
    text-decoration: none;
    transition: var(--transition);
}

.meta-categories a:hover {
    background: var(--primary-color);
    color: white;
}

.result-excerpt {
    color: var(--muted-text);
    line-height: 1.5;
    margin-bottom: 15px;
    font-size: 14px;
}

.result-excerpt mark {
    background: rgba(255, 107, 107, 0.3);
    color: var(--primary-color);
    padding: 1px 3px;
    border-radius: 2px;
}

.result-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.read-more-btn {
    background: var(--gradient-primary);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: var(--transition);
}

.read-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.result-tags {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.result-tag {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
}

/* No Results */
.no-search-results {
    text-align: center;
    padding: 60px 20px;
    max-width: 700px;
    margin: 0 auto;
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-search-results h2 {
    margin-bottom: 15px;
    color: var(--muted-text);
}

.no-search-results p {
    color: var(--muted-text);
    margin-bottom: 40px;
    font-size: 16px;
    line-height: 1.6;
}

.search-suggestions-container {
    margin-bottom: 40px;
    text-align: left;
}

.search-suggestions-container h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 18px;
}

.search-tips {
    color: var(--muted-text);
    line-height: 1.8;
}

.search-tips li {
    margin-bottom: 8px;
}

.alternative-searches,
.browse-categories {
    margin-bottom: 40px;
}

.alternative-searches h3,
.browse-categories h3 {
    color: var(--primary-color);
    margin-bottom: 20px;
    font-size: 18px;
}

.popular-searches-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.popular-search-tag {
    background: var(--hover-bg);
    color: var(--muted-text);
    padding: 8px 15px;
    border-radius: 20px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.popular-search-tag:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
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

.category-name {
    display: block;
    color: var(--light-text);
    font-weight: 600;
    margin-bottom: 5px;
}

.category-count {
    color: var(--muted-text);
    font-size: 13px;
}

/* Responsive */
@media (min-width: 768px) {
    .results-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .search-filters {
        flex-wrap: nowrap;
    }
}

@media (min-width: 1024px) {
    .results-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .search-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        justify-content: space-between;
    }
    
    .results-grid.list-view .result-card {
        flex-direction: column;
    }
    
    .results-grid.list-view .result-thumbnail {
        width: 100%;
        height: 180px;
    }
    
    .result-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sort functionality
    const sortSelect = document.getElementById('search-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('orderby', this.value);
            currentUrl.searchParams.delete('paged');
            window.location.href = currentUrl.toString();
        });
    }
    
    // Content type filter
    const contentTypeSelect = document.getElementById('content-type');
    if (contentTypeSelect) {
        contentTypeSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            if (this.value === 'all') {
                currentUrl.searchParams.delete('post_type');
            } else {
                currentUrl.searchParams.set('post_type', this.value);
            }
            currentUrl.searchParams.delete('paged');
            window.location.href = currentUrl.toString();
        });
    }
    
    // View toggle functionality
    const viewButtons = document.querySelectorAll('.view-btn');
    const resultsContainer = document.getElementById('results-container');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (view === 'list') {
                resultsContainer.classList.add('list-view');
            } else {
                resultsContainer.classList.remove('list-view');
            }
            
            // Save preference
            localStorage.setItem('searchView', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('searchView');
    if (savedView === 'list') {
        document.querySelector('[data-view="list"]')?.click();
    }
    
    // Track search analytics
    if (typeof gtag !== 'undefined') {
        const searchQuery = new URLSearchParams(window.location.search).get('s');
        const resultsCount = document.querySelectorAll('.result-card').length;
        
        if (searchQuery) {
            gtag('event', 'search', {
                'search_term': searchQuery,
                'results_count': resultsCount
            });
        }
    }
    
    // Animation delays for result cards
    const resultCards = document.querySelectorAll('.result-card');
    resultCards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.05) + 's';
    });
});
</script>

<?php get_footer(); ?>