<?php
/**
 * Custom Widgets for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register widget areas
 */
function videoplayer_widgets_init() {
    // Main Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar Principal', 'videoplayer'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Widgets que aparecen en el sidebar principal.', 'videoplayer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Video Archive Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar de Videos', 'videoplayer'),
        'id'            => 'video-archive-sidebar',
        'description'   => esc_html__('Widgets para la página de archivo de videos.', 'videoplayer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Single Video Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar de Video Individual', 'videoplayer'),
        'id'            => 'video-sidebar',
        'description'   => esc_html__('Widgets para páginas de videos individuales.', 'videoplayer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Search Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar de Búsqueda', 'videoplayer'),
        'id'            => 'search-sidebar',
        'description'   => esc_html__('Widgets para la página de resultados de búsqueda.', 'videoplayer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Footer Widgets
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer %d', 'videoplayer'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Widgets para la columna %d del footer.', 'videoplayer'), $i),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }
}
add_action('widgets_init', 'videoplayer_widgets_init');

/**
 * Popular Videos Widget
 */
class VideoPlayer_Popular_Videos_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_popular_videos',
            esc_html__('Videos Populares', 'videoplayer'),
            array(
                'description' => esc_html__('Muestra los videos más populares.', 'videoplayer'),
                'classname' => 'widget_popular_videos',
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Populares', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $period = !empty($instance['period']) ? $instance['period'] : 'all';

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $query_args = array(
            'post_type' => 'video',
            'posts_per_page' => $number,
            'meta_key' => '_view_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_view_count',
                    'compare' => 'EXISTS'
                )
            )
        );

        if ($period !== 'all') {
            $days = ($period === 'week') ? 7 : (($period === 'month') ? 30 : 365);
            $query_args['date_query'] = array(
                array(
                    'after' => $days . ' days ago'
                )
            );
        }

        $popular_videos = new WP_Query($query_args);

        if ($popular_videos->have_posts()) {
            echo '<div class="popular-videos-widget">';
            $count = 1;
            
            while ($popular_videos->have_posts()) {
                $popular_videos->the_post();
                ?>
                <div class="popular-video-item" data-rank="<?php echo $count; ?>">
                    <div class="video-rank"><?php echo $count; ?></div>
                    
                    <div class="video-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                <div class="thumbnail-placeholder">▶</div>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="video-info">
                        <h4 class="video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="video-meta">
                            <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                            <span class="duration"><?php echo get_video_duration(); ?></span>
                        </div>
                    </div>
                </div>
                <?php
                $count++;
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No hay videos populares disponibles.', 'videoplayer') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Populares', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $period = !empty($instance['period']) ? $instance['period'] : 'all';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_attr_e('Número de videos:', 'videoplayer'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('period')); ?>"><?php esc_attr_e('Período:', 'videoplayer'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('period')); ?>" name="<?php echo esc_attr($this->get_field_name('period')); ?>">
                <option value="all" <?php selected($period, 'all'); ?>><?php esc_html_e('Todo el tiempo', 'videoplayer'); ?></option>
                <option value="week" <?php selected($period, 'week'); ?>><?php esc_html_e('Última semana', 'videoplayer'); ?></option>
                <option value="month" <?php selected($period, 'month'); ?>><?php esc_html_e('Último mes', 'videoplayer'); ?></option>
                <option value="year" <?php selected($period, 'year'); ?>><?php esc_html_e('Último año', 'videoplayer'); ?></option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['period'] = (!empty($new_instance['period'])) ? sanitize_text_field($new_instance['period']) : 'all';

        return $instance;
    }
}

/**
 * Recent Videos Widget
 */
class VideoPlayer_Recent_Videos_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_recent_videos',
            esc_html__('Videos Recientes', 'videoplayer'),
            array(
                'description' => esc_html__('Muestra los videos más recientes.', 'videoplayer'),
                'classname' => 'widget_recent_videos',
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Recientes', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = !empty($instance['show_date']);

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $recent_videos = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => $number,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ($recent_videos->have_posts()) {
            echo '<div class="recent-videos-widget">';
            
            while ($recent_videos->have_posts()) {
                $recent_videos->the_post();
                ?>
                <div class="recent-video-item">
                    <div class="video-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                <div class="thumbnail-placeholder">▶</div>
                            <?php endif; ?>
                            <div class="video-duration"><?php echo get_video_duration(); ?></div>
                        </a>
                    </div>
                    
                    <div class="video-info">
                        <h4 class="video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        
                        <div class="video-meta">
                            <?php if ($show_date) : ?>
                                <span class="date"><?php echo get_the_date(); ?></span>
                            <?php endif; ?>
                            <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                        </div>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Recientes', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = !empty($instance['show_date']);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_attr_e('Número de videos:', 'videoplayer'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>"><?php esc_attr_e('Mostrar fecha', 'videoplayer'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['show_date'] = (!empty($new_instance['show_date'])) ? 1 : 0;

        return $instance;
    }
}

/**
 * Video Categories Widget
 */
class VideoPlayer_Video_Categories_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_video_categories',
            esc_html__('Categorías de Videos', 'videoplayer'),
            array(
                'description' => esc_html__('Muestra las categorías de videos con contadores.', 'videoplayer'),
                'classname' => 'widget_video_categories',
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Categorías', 'videoplayer');
        $count = !empty($instance['count']);
        $hierarchical = !empty($instance['hierarchical']);
        $dropdown = !empty($instance['dropdown']);

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $cat_args = array(
            'orderby' => 'name',
            'show_count' => $count,
            'hierarchical' => $hierarchical,
            'title_li' => '',
            'hide_empty' => 1,
        );

        if ($dropdown) {
            ?>
            <form action="<?php echo esc_url(home_url()); ?>" method="get">
                <select name="cat" id="cat" class="postform">
                    <option value=""><?php echo esc_attr(__('Seleccionar categoría', 'videoplayer')); ?></option>
                    <?php wp_list_categories(array_merge($cat_args, array('walker' => new Walker_CategoryDropdown()))); ?>
                </select>
                <input type="submit" value="<?php esc_attr_e('Ver', 'videoplayer'); ?>">
            </form>
            <?php
        } else {
            ?>
            <ul class="video-categories-list">
                <?php wp_list_categories($cat_args); ?>
            </ul>
            <?php
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Categorías', 'videoplayer');
        $count = !empty($instance['count']);
        $hierarchical = !empty($instance['hierarchical']);
        $dropdown = !empty($instance['dropdown']);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>" <?php checked($dropdown); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php esc_attr_e('Mostrar como dropdown', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" <?php checked($count); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_attr_e('Mostrar contadores', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>" <?php checked($hierarchical); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php esc_attr_e('Mostrar jerarquía', 'videoplayer'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

        return $instance;
    }
}

/**
 * Video Search Widget
 */
class VideoPlayer_Video_Search_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_video_search',
            esc_html__('Búsqueda de Videos', 'videoplayer'),
            array(
                'description' => esc_html__('Formulario de búsqueda específico para videos.', 'videoplayer'),
                'classname' => 'widget_video_search',
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Buscar Videos', 'videoplayer');
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : esc_html__('Buscar videos...', 'videoplayer');

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        ?>
        <form role="search" method="get" class="video-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-input-wrapper">
                <input type="search" 
                       class="search-field" 
                       placeholder="<?php echo esc_attr($placeholder); ?>" 
                       value="<?php echo get_search_query(); ?>" 
                       name="s" 
                       required>
                <input type="hidden" name="post_type" value="video">
                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Buscar', 'videoplayer'); ?>">
                    <span class="search-icon">🔍</span>
                </button>
            </div>
        </form>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Buscar Videos', 'videoplayer');
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : esc_html__('Buscar videos...', 'videoplayer');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('placeholder')); ?>"><?php esc_attr_e('Texto del placeholder:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('placeholder')); ?>" name="<?php echo esc_attr($this->get_field_name('placeholder')); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['placeholder'] = sanitize_text_field($new_instance['placeholder']);

        return $instance;
    }
}

/**
 * Featured Videos Widget
 */
class VideoPlayer_Featured_Videos_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_featured_videos',
            esc_html__('Videos Destacados', 'videoplayer'),
            array(
                'description' => esc_html__('Muestra videos marcados como destacados.', 'videoplayer'),
                'classname' => 'widget_featured_videos',
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Destacados', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'list';

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $featured_videos = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => $number,
            'meta_query' => array(
                array(
                    'key' => '_featured_video',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ($featured_videos->have_posts()) {
            echo '<div class="featured-videos-widget layout-' . esc_attr($layout) . '">';
            
            while ($featured_videos->have_posts()) {
                $featured_videos->the_post();
                ?>
                <div class="featured-video-item">
                    <div class="video-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail($layout === 'grid' ? 'medium' : 'thumbnail'); ?>
                            <?php else : ?>
                                <div class="thumbnail-placeholder">⭐</div>
                            <?php endif; ?>
                            <div class="featured-badge"><?php esc_html_e('Destacado', 'videoplayer'); ?></div>
                        </a>
                    </div>
                    
                    <div class="video-info">
                        <h4 class="video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        
                        <?php if ($layout === 'grid') : ?>
                            <div class="video-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="video-meta">
                            <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                            <span class="duration"><?php echo get_video_duration(); ?></span>
                        </div>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No hay videos destacados disponibles.', 'videoplayer') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Videos Destacados', 'videoplayer');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'list';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_attr_e('Número de videos:', 'videoplayer'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>"><?php esc_attr_e('Layout:', 'videoplayer'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
                <option value="list" <?php selected($layout, 'list'); ?>><?php esc_html_e('Lista', 'videoplayer'); ?></option>
                <option value="grid" <?php selected($layout, 'grid'); ?>><?php esc_html_e('Cuadrícula', 'videoplayer'); ?></option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['layout'] = sanitize_text_field($new_instance['layout']);

        return $instance;
    }
}

/**
 * Register custom widgets
 */
function videoplayer_register_widgets() {
    register_widget('VideoPlayer_Popular_Videos_Widget');
    register_widget('VideoPlayer_Recent_Videos_Widget');
    register_widget('VideoPlayer_Video_Categories_Widget');
    register_widget('VideoPlayer_Video_Search_Widget');
    register_widget('VideoPlayer_Featured_Videos_Widget');
}
add_action('widgets_init', 'videoplayer_register_widgets');

/**
 * Add widget styles
 */
function videoplayer_widget_styles() {
    ?>
    <style>
    /* Popular Videos Widget */
    .widget_popular_videos .popular-video-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .widget_popular_videos .popular-video-item:last-child {
        border-bottom: none;
    }

    .widget_popular_videos .video-rank {
        background: var(--primary-color);
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .widget_popular_videos .video-thumbnail {
        width: 60px;
        height: 45px;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }

    .widget_popular_videos .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .widget_popular_videos .video-info {
        flex: 1;
        min-width: 0;
    }

    .widget_popular_videos .video-title {
        font-size: 13px;
        margin: 0 0 5px 0;
        line-height: 1.3;
    }

    .widget_popular_videos .video-title a {
        color: var(--light-text);
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .widget_popular_videos .video-title a:hover {
        color: var(--primary-color);
    }

    .widget_popular_videos .video-meta {
        font-size: 11px;
        color: var(--muted-text);
        display: flex;
        gap: 8px;
    }

    /* Recent Videos Widget */
    .widget_recent_videos .recent-video-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .widget_recent_videos .recent-video-item:last-child {
        border-bottom: none;
    }

    .widget_recent_videos .video-thumbnail {
        width: 70px;
        height: 50px;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }

    .widget_recent_videos .video-duration {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 1px 4px;
        border-radius: 2px;
        font-size: 9px;
    }

    /* Video Categories Widget */
    .widget_video_categories .video-categories-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .widget_video_categories .video-categories-list li {
        border-bottom: 1px solid var(--border-color);
        padding: 8px 0;
    }

    .widget_video_categories .video-categories-list li:last-child {
        border-bottom: none;
    }

    .widget_video_categories .video-categories-list a {
        color: var(--light-text);
        text-decoration: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: var(--transition);
    }

    .widget_video_categories .video-categories-list a:hover {
        color: var(--primary-color);
    }

    /* Video Search Widget */
    .widget_video_search .video-search-form {
        position: relative;
    }

    .widget_video_search .search-input-wrapper {
        display: flex;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--border-color);
        border-radius: 25px;
        overflow: hidden;
    }

    .widget_video_search .search-field {
        flex: 1;
        background: none;
        border: none;
        color: var(--light-text);
        padding: 10px 15px;
        outline: none;
        font-size: 14px;
    }

    .widget_video_search .search-field::placeholder {
        color: var(--muted-text);
    }

    .widget_video_search .search-submit {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 10px 15px;
        cursor: pointer;
        transition: var(--transition);
    }

    .widget_video_search .search-submit:hover {
        background: var(--secondary-color);
    }

    /* Featured Videos Widget */
    .widget_featured_videos .featured-video-item {
        padding: 15px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .widget_featured_videos .featured-video-item:last-child {
        border-bottom: none;
    }

    .widget_featured_videos.layout-list .featured-video-item {
        display: flex;
        gap: 12px;
    }

    .widget_featured_videos.layout-list .video-thumbnail {
        width: 80px;
        height: 60px;
        flex-shrink: 0;
    }

    .widget_featured_videos.layout-grid .video-thumbnail {
        width: 100%;
        height: 120px;
        margin-bottom: 10px;
    }

    .widget_featured_videos .video-thumbnail {
        position: relative;
        border-radius: 6px;
        overflow: hidden;
        background: var(--darker-bg);
    }

    .widget_featured_videos .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .widget_featured_videos .featured-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: var(--primary-color);
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .widget_featured_videos .video-title {
        font-size: 14px;
        margin: 0 0 8px 0;
        line-height: 1.3;
    }

    .widget_featured_videos .video-title a {
        color: var(--light-text);
        text-decoration: none;
    }

    .widget_featured_videos .video-title a:hover {
        color: var(--primary-color);
    }

    .widget_featured_videos .video-excerpt {
        font-size: 12px;
        color: var(--muted-text);
        line-height: 1.4;
        margin-bottom: 8px;
    }

    .widget_featured_videos .video-meta {
        font-size: 11px;
        color: var(--muted-text);
        display: flex;
        gap: 8px;
    }

    /* General Widget Styles */
    .widget {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 25px;
    }

    .widget-title {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border-color);
        position: relative;
    }

    .widget-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--primary-color);
    }

    .thumbnail-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(45deg, #333, #555);
        color: rgba(255, 255, 255, 0.6);
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .widget {
            padding: 15px;
        }
        
        .widget_popular_videos .video-rank {
            width: 20px;
            height: 20px;
            font-size: 10px;
        }
        
        .widget_popular_videos .video-thumbnail,
        .widget_recent_videos .video-thumbnail {
            width: 50px;
            height: 38px;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'videoplayer_widget_styles');