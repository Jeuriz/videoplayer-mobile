<?php
/**
 * Search Results Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <div class="search-header">
        <h1 class="search-title">
            <?php
            printf(
                esc_html__('Resultados de búsqueda para: %s', 'videoplayer'),
                '<span class="search-term">' . get_search_query() . '</span>'
            );
            ?>
        </h1>
        
        <div class="search-stats">
            <?php
            global $wp_query;
            if ($wp_query->found_posts) {
                printf(
                    esc_html(_n('Se encontró %d resultado', 'Se encontraron %d resultados', $wp_query->found_posts, 'videoplayer')),
                    number_format_i18n($wp_query->found_posts)
                );
            } else {
                esc_html_e('No se encontraron resultados', 'videoplayer');
            }
            ?>
        </div>
    </div>

    <!-- Enhanced Search Form -->
    <div class="search-form-container">
        <form role="search" method="get" class="enhanced-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-input-group">
                <input type="search" 
                       class="search-field" 
                       placeholder="<?php esc_attr_e('¿Qué estás buscando?', 'videoplayer'); ?>" 
                       value="<?php echo get_search_query(); ?>" 
                       name="s" 
                       autocomplete="off">
                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Buscar', 'videoplayer'); ?>">
                    <span class="icon">🔍</span>
                </button>
            </div>
            
            <div class="search-filters">
                <div class="filter-group">
                    <label for="search-post-type"><?php esc_html_e('Buscar en:', 'videoplayer'); ?></label>
                    <select name="post_type" id="search-post-type">
                        <option value="" <?php selected(get_query_var('post_type'), ''); ?>><?php esc_html_e('Todo el contenido', 'videoplayer'); ?></option>
                        <option value="video" <?php selected(get_query_var('post_type'), 'video'); ?>><?php esc_html_e('Solo videos', 'videoplayer'); ?></option>
                        <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>><?php esc_html_e('Solo artículos', 'videoplayer'); ?></option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search-category"><?php esc_html_e('Categoría:', 'videoplayer'); ?></label>
                    <select name="category_name" id="search-category">
                        <option value=""><?php esc_html_e('Todas las categorías', 'videoplayer'); ?></option>
                        <?php
                        $categories = get_categories(array('hide_empty' => true));
                        foreach ($categories as $category) {
                            $selected = (get_query_var('category_name') == $category->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>';
                            echo esc_html($category->name) . ' (' . $category->count . ')';
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search-date"><?php esc_html_e('Fecha:', 'videoplayer'); ?></label>
                    <select name="date_filter" id="search-date">
                        <option value=""><?php esc_html_e('Cualquier fecha', 'videoplayer'); ?></option>
                        <option value="week" <?php selected(get_query_var('date_filter'), 'week'); ?>><?php esc_html_e('Última semana', 'videoplayer'); ?></option>
                        <option value="month" <?php selected(get_query_var('date_filter'), 'month'); ?>><?php esc_html_e('Último mes', 'videoplayer'); ?></option>
                        <option value="year" <?php selected(get_query_var('date_filter'), 'year'); ?>><?php esc_html_e('Último año', 'videoplayer'); ?></option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Search Suggestions -->
    <?php if (!have_posts() && get_search_query()) : ?>
        <div class="search-suggestions">
            <h3><?php esc_html_e('Sugerencias de búsqueda:', 'videoplayer'); ?></h3>
            <ul class="suggestions-list">
                <li><?php esc_html_e('Verifica la ortografía de las palabras', 'videoplayer'); ?></li>
                <li><?php esc_html_e('Intenta con términos más generales', 'videoplayer'); ?></li>
                <li><?php esc_html_e('Usa menos palabras clave', 'videoplayer'); ?></li>
                <li><?php esc_html_e('Prueba con sinónimos', 'videoplayer'); ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Search Results -->
    <main class="search-results">
        <?php if (have_posts()) : ?>
            
            <!-- Results Filter -->
            <div class="results-filter">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-type="all">
                        <?php esc_html_e('Todo', 'videoplayer'); ?> 
                        <span class="count">(<?php echo $wp_query->found_posts; ?>)</span>
                    </button>
                    
                    <?php
                    // Count posts by type
                    $video_count = 0;
                    $post_count = 0;
                    
                    $temp_query = clone $wp_query;
                    if ($temp_query->have_posts()) {
                        while ($temp_query->have_posts()) {
                            $temp_query->the_post();
                            if (get_post_type() == 'video') {
                                $video_count++;
                            } else {
                                $post_count++;
                            }
                        }
                        wp_reset_postdata();
                    }
                    ?>
                    
                    <?php if ($video_count > 0) : ?>
                        <button class="filter-tab" data-type="video">
                            <?php esc_html_e('Videos', 'videoplayer'); ?> 
                            <span class="count">(<?php echo $video_count; ?>)</span>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($post_count > 0) : ?>
                        <button class="filter-tab" data-type="post">
                            <?php esc_html_e('Artículos', 'videoplayer'); ?> 
                            <span class="count">(<?php echo $post_count; ?>)</span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="results-sort">
                    <label for="sort-results"><?php esc_html_e('Ordenar por:', 'videoplayer'); ?></label>
                    <select id="sort-results">
                        <option value="relevance"><?php esc_html_e('Relevancia', 'videoplayer'); ?></option>
                        <option value="date"><?php esc_html_e('Más recientes', 'videoplayer'); ?></option>
                        <option value="title"><?php esc_html_e('Título A-Z', 'videoplayer'); ?></option>
                        <option value="popularity"><?php esc_html_e('Popularidad', 'videoplayer'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Results List -->
            <div class="results-list" id="search-results">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?> data-post-type="<?php echo get_post_type(); ?>">
                        
                        <?php if (get_post_type() == 'video') : ?>
                            <!-- Video Result -->
                            <div class="result-thumbnail video-thumbnail">
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
                                    
                                    <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                    
                                    <div class="post-type-badge video-badge">
                                        <span class="icon">📹</span>
                                        <?php esc_html_e('Video', 'videoplayer'); ?>
                                    </div>
                                </a>
                            </div>
                            
                        <?php else : ?>
                            <!-- Post Result -->
                            <div class="result-thumbnail post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="thumbnail-placeholder">
                                            <span class="post-icon">📄</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-type-badge post-badge">
                                        <span class="icon">📝</span>
                                        <?php esc_html_e('Artículo', 'videoplayer'); ?>
                                    </div>
                                </a>
                            </div>
                            
                        <?php endif; ?>
                        
                        <div class="result-content">
                            <header class="result-header">
                                <h2 class="result-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="result-meta">
                                    <span class="result-date">
                                        📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
                                    </span>
                                    
                                    <span class="result-author">
                                        👤 <?php the_author(); ?>
                                    </span>
                                    
                                    <?php if (get_post_type() == 'video') : ?>
                                        <span class="result-views">
                                            👁️ <?php echo number_format(get_video_views()); ?> vistas
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (has_category()) : ?>
                                        <span class="result-category">
                                            📁 <?php the_category(', '); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </header>
                            
                            <div class="result-excerpt">
                                <?php
                                $excerpt = get_the_excerpt();
                                $search_term = get_search_query();
                                
                                if ($search_term && $excerpt) {
                                    // Highlight search terms in excerpt
                                    $highlighted_excerpt = preg_replace(
                                        '/(' . preg_quote($search_term, '/') . ')/i',
                                        '<mark>$1</mark>',
                                        $excerpt
                                    );
                                    echo $highlighted_excerpt;
                                } else {
                                    echo $excerpt;
                                }
                                ?>
                            </div>
                            
                            <?php if (has_tag()) : ?>
                                <div class="result-tags">
                                    <?php the_tags('<span class="tag">#', '</span><span class="tag">#', '</span>'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="result-actions">
                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                    <?php echo (get_post_type() == 'video') ? esc_html__('Ver video', 'videoplayer') : esc_html__('Leer más', 'videoplayer'); ?>
                                    <span class="arrow">→</span>
                                </a>
                                
                                <div class="result-share">
                                    <button class="share-btn" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>">
                                        <span class="icon">📤</span>
                                        <?php esc_html_e('Compartir', 'videoplayer'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </article>
                    
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <nav class="search-pagination" role="navigation" aria-label="<?php esc_attr_e('Navegación de resultados', 'videoplayer'); ?>">
                <?php
                echo paginate_links(array(
                    'prev_text' => '← ' . __('Anterior', 'videoplayer'),
                    'next_text' => __('Siguiente', 'videoplayer') . ' →',
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Página', 'videoplayer') . ' </span>',
                ));
                ?>
            </nav>

        <?php else : ?>
            
            <!-- No Results -->
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h2><?php esc_html_e('No se encontraron resultados', 'videoplayer'); ?></h2>
                <p><?php esc_html_e('No pudimos encontrar contenido que coincida con tu búsqueda. Prueba con otros términos o navega por nuestras categorías.', 'videoplayer'); ?></p>
                
                <!-- Popular Content -->
                <div class="popular-content">
                    <h3><?php esc_html_e('Contenido popular:', 'videoplayer'); ?></h3>
                    
                    <div class="popular-items">
                        <?php
                        $popular_videos = new WP_Query(array(
                            'post_type' => 'video',
                            'posts_per_page' => 3,
                            'meta_key' => '_view_count',
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC'
                        ));
                        
                        if ($popular_videos->have_posts()) :
                            while ($popular_videos->have_posts()) : $popular_videos->the_post();
                        ?>
                            <div class="popular-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php endif; ?>
                                    <div class="popular-item-content">
                                        <h4><?php the_title(); ?></h4>
                                        <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                                    </div>
                                </a>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="browse-categories">
                    <h3><?php esc_html_e('Explorar categorías:', 'videoplayer'); ?></h3>
                    <div class="categories-grid">
                        <?php
                        $categories = get_categories(array(
                            'hide_empty' => true,
                            'number' => 6,
                            'orderby' => 'count',
                            'order' => 'DESC'
                        ));
                        
                        foreach ($categories as $category) :
                        ?>
                            <a href="<?php echo get_category_link($category->term_id); ?>" class="category-card">
                                <h4><?php echo esc_html($category->name); ?></h4>
                                <span class="count"><?php echo $category->count; ?> posts</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
    </main>
</div>

<!-- Sidebar -->
<?php if (is_active_sidebar('search-sidebar')) : ?>
    <aside class="search-sidebar">
        <?php dynamic_sidebar('search-sidebar'); ?>
    </aside>
<?php endif; ?>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.search-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
    border-bottom: 1px solid var(--border-color);
}

.search-title {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.search-term {
    color: var(--primary-color);
    font-weight: 700;
}

.search-stats {
    color: var(--muted-text);
    font-size: 16px;
}

.search-form-container {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 30px;
    margin-bottom: 40px;
    border: 1px solid var(--border-color);
}

.enhanced-search-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.search-input-group {
    display: flex;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto;
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
    font-size: 16px;
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

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
}

.filter-group label {
    font-size: 14px;
    color: var(--muted-text);
    font-weight: 600;
}

.filter-group select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 8px 15px;
    border-radius: 6px;
    min-width: 150px;
}

.search-suggestions {
    background: rgba(255, 107, 107, 0.1);
    border: 1px solid rgba(255, 107, 107, 0.3);
    border-radius: var(--border-radius);
    padding: 25px;
    margin-bottom: 30px;
}

.search-suggestions h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
}

.suggestions-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.suggestions-list li {
    color: var(--muted-text);
    padding: 5px 0;
    padding-left: 20px;
    position: relative;
}

.suggestions-list li::before {
    content: '💡';
    position: absolute;
    left: 0;
}

.results-filter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.filter-tabs {
    display: flex;
    gap: 10px;
}

.filter-tab {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tab:hover,
.filter-tab.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.filter-tab .count {
    font-size: 12px;
    opacity: 0.8;
}

.results-sort {
    display: flex;
    align-items: center;
    gap: 10px;
}

.results-sort label {
    font-size: 14px;
    color: var(--muted-text);
}

.results-sort select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 8px 15px;
    border-radius: 6px;
}

.results-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 40px;
}

.search-result-item {
    display: flex;
    gap: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 25px;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.search-result-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.result-thumbnail {
    flex: 0 0 200px;
    height: 150px;
    position: relative;
    border-radius: var(--border-radius);
    overflow: hidden;
    background: var(--darker-bg);
}

.result-thumbnail img {
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
}

.video-icon,
.post-icon {
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

.search-result-item:hover .video-overlay {
    opacity: 1;
}

.play-btn {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.search-result-item:hover .play-btn {
    background: var(--primary-color);
}

.play-icon {
    width: 0;
    height: 0;
    border-left: 12px solid var(--dark-bg);
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    margin-left: 2px;
}

.search-result-item:hover .play-icon {
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

.post-type-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}

.video-badge {
    background: var(--primary-color);
    color: white;
}

.post-badge {
    background: var(--secondary-color);
    color: white;
}

.result-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.result-title {
    font-size: 1.4rem;
    margin: 0;
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

.result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 14px;
    color: var(--muted-text);
}

.result-excerpt {
    color: var(--muted-text);
    line-height: 1.6;
    font-size: 15px;
}

.result-excerpt mark {
    background: rgba(255, 107, 107, 0.3);
    color: var(--primary-color);
    padding: 2px 4px;
    border-radius: 3px;
}

.result-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.tag {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    text-decoration: none;
    transition: var(--transition);
}

.tag:hover {
    background: var(--primary-color);
    color: white;
}

.result-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.read-more-btn {
    background: var(--gradient-primary);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
}

.read-more-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-light);
}

.share-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.share-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: var(--light-text);
}

.search-pagination {
    text-align: center;
    margin: 40px 0;
}

.search-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.search-pagination .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    text-decoration: none;
    border-radius: 6px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.search-pagination .page-numbers:hover,
.search-pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-results h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--light-text);
}

.no-results p {
    font-size: 1.1rem;
    color: var(--muted-text);
    max-width: 600px;
    margin: 0 auto 40px;
    line-height: 1.6;
}

.popular-content,
.browse-categories {
    margin-bottom: 40px;
}

.popular-content h3,
.browse-categories h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.popular-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.popular-item a {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 15px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.popular-item a:hover {
    background: rgba(255, 255, 255, 0.08);
}

.popular-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
}

.popular-item-content h4 {
    color: var(--light-text);
    margin-bottom: 5px;
    font-size: 14px;
}

.popular-item-content .views {
    color: var(--muted-text);
    font-size: 12px;
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
    text-align: center;
    transition: var(--transition);
}

.category-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.category-card h4 {
    color: var(--light-text);
    margin-bottom: 8px;
}

.category-card .count {
    color: var(--muted-text);
    font-size: 14px;
}

@media (max-width: 768px) {
    .search-filters {
        flex-direction: column;
        gap: 15px;
    }
    
    .results-filter {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-tabs {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .search-result-item {
        flex-direction: column;
    }
    
    .result-thumbnail {
        flex: none;
        height: 200px;
    }
    
    .result-actions {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .popular-items {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}

@media (max-width: 480px) {
    .search-title {
        font-size: 1.5rem;
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
    initSearchFunctionality();
});

function initSearchFunctionality() {
    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    const resultItems = document.querySelectorAll('.search-result-item');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter results
            resultItems.forEach(item => {
                if (type === 'all' || item.dataset.postType === type) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Share functionality
    const shareButtons = document.querySelectorAll('.share-btn');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const title = this.dataset.title;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url);
                alert('¡Enlace copiado al portapapeles!');
            }
        });
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sort-results');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            // Implement sorting logic or reload page with sort parameter
            const url = new URL(window.location);
            url.searchParams.set('orderby', sortBy);
            window.location.href = url.toString();
        });
    }
}

function handleVideoClick(event) {
    // Use the global function from main.js
    if (window.VideoPlayerTheme) {
        window.VideoPlayerTheme.handleVideoClick(event);
    }
}
</script>

<?php get_footer(); ?>