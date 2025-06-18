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
 * Popular Videos Widget
 */
class VideoPlayer_Popular_Videos_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_popular_videos',
            __('📺 Videos Populares', 'videoplayer'),
            array(
                'description' => __('Muestra los videos más vistos del sitio', 'videoplayer'),
                'classname' => 'widget-popular-videos'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_views = isset($instance['show_views']) ? (bool) $instance['show_views'] : true;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $videos = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => $number,
            'meta_key' => '_view_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'post_status' => 'publish'
        ));
        
        if ($videos->have_posts()) {
            echo '<div class="popular-videos-widget">';
            $rank = 1;
            
            while ($videos->have_posts()) {
                $videos->the_post();
                ?>
                <div class="popular-video-item" data-rank="<?php echo $rank; ?>">
                    <div class="video-rank"><?php echo $rank; ?></div>
                    
                    <div class="video-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                <div class="thumbnail-placeholder">▶</div>
                            <?php endif; ?>
                            
                            <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                        </a>
                    </div>
                    
                    <div class="video-info">
                        <h4 class="video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        
                        <div class="video-meta">
                            <?php if ($show_views) : ?>
                                <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                            <?php endif; ?>
                            
                            <?php if ($show_date) : ?>
                                <span class="date"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
                $rank++;
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No hay videos disponibles.', 'videoplayer') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Videos Populares', 'videoplayer');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_views = isset($instance['show_views']) ? (bool) $instance['show_views'] : true;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Número de videos:', 'videoplayer'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $number; ?>" size="3">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_views); ?> id="<?php echo $this->get_field_id('show_views'); ?>" name="<?php echo $this->get_field_name('show_views'); ?>">
            <label for="<?php echo $this->get_field_id('show_views'); ?>"><?php _e('Mostrar número de visualizaciones', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>">
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Mostrar fecha', 'videoplayer'); ?></label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['show_views'] = isset($new_instance['show_views']) ? (bool) $new_instance['show_views'] : false;
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        
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
            __('📁 Categorías de Videos', 'videoplayer'),
            array(
                'description' => __('Muestra las categorías de videos con contadores', 'videoplayer'),
                'classname' => 'widget-video-categories'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $number = isset($instance['number']) ? absint($instance['number']) : 8;
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $orderby = isset($instance['orderby']) ? $instance['orderby'] : 'count';
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $categories = get_categories(array(
            'taxonomy' => 'category',
            'hide_empty' => true,
            'number' => $number,
            'orderby' => $orderby,
            'order' => 'DESC'
        ));
        
        if (!empty($categories)) {
            echo '<div class="video-categories-widget">';
            
            foreach ($categories as $category) {
                $category_icon = get_term_meta($category->term_id, 'category_icon', true) ?: '📹';
                ?>
                <div class="category-item">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-link">
                        <span class="category-icon"><?php echo esc_html($category_icon); ?></span>
                        <span class="category-info">
                            <span class="category-name"><?php echo esc_html($category->name); ?></span>
                            <?php if ($show_count) : ?>
                                <span class="category-count"><?php echo $category->count; ?> videos</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </div>
                <?php
            }
            
            echo '</div>';
        } else {
            echo '<p>' . __('No hay categorías disponibles.', 'videoplayer') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Categorías de Videos', 'videoplayer');
        $number = isset($instance['number']) ? absint($instance['number']) : 8;
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $orderby = isset($instance['orderby']) ? $instance['orderby'] : 'count';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Número de categorías:', 'videoplayer'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $number; ?>" size="3">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Ordenar por:', 'videoplayer'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
                <option value="count" <?php selected($orderby, 'count'); ?>><?php _e('Cantidad de videos', 'videoplayer'); ?></option>
                <option value="name" <?php selected($orderby, 'name'); ?>><?php _e('Nombre', 'videoplayer'); ?></option>
                <option value="id" <?php selected($orderby, 'id'); ?>><?php _e('ID', 'videoplayer'); ?></option>
            </select>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>">
            <label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e('Mostrar contador de videos', 'videoplayer'); ?></label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 8;
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        $instance['orderby'] = isset($new_instance['orderby']) ? sanitize_text_field($new_instance['orderby']) : 'count';
        
        return $instance;
    }
}

/**
 * Live Stats Widget
 */
class VideoPlayer_Live_Stats_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_live_stats',
            __('📊 Estadísticas en Vivo', 'videoplayer'),
            array(
                'description' => __('Muestra estadísticas del sitio en tiempo real', 'videoplayer'),
                'classname' => 'widget-live-stats'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $show_videos = isset($instance['show_videos']) ? (bool) $instance['show_videos'] : true;
        $show_views = isset($instance['show_views']) ? (bool) $instance['show_views'] : true;
        $show_comments = isset($instance['show_comments']) ? (bool) $instance['show_comments'] : true;
        $show_users = isset($instance['show_users']) ? (bool) $instance['show_users'] : true;
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        echo '<div class="live-stats-widget">';
        echo '<div class="stats-grid">';
        
        if ($show_videos) {
            $video_count = wp_count_posts('video')->publish;
            ?>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo $video_count; ?>"><?php echo $video_count; ?></div>
                <div class="stat-label"><?php _e('Videos', 'videoplayer'); ?></div>
            </div>
            <?php
        }
        
        if ($show_views) {
            global $wpdb;
            $total_views = $wpdb->get_var("
                SELECT SUM(meta_value) 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = '_view_count'
            ");
            $total_views = $total_views ?: 0;
            ?>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo $total_views; ?>"><?php echo number_format($total_views); ?></div>
                <div class="stat-label"><?php _e('Visualizaciones', 'videoplayer'); ?></div>
            </div>
            <?php
        }
        
        if ($show_comments) {
            $comment_count = wp_count_comments()->approved;
            ?>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo $comment_count; ?>"><?php echo $comment_count; ?></div>
                <div class="stat-label"><?php _e('Comentarios', 'videoplayer'); ?></div>
            </div>
            <?php
        }
        
        if ($show_users) {
            ?>
            <div class="stat-item">
                <div class="stat-number" id="online-users">...</div>
                <div class="stat-label"><?php _e('Online', 'videoplayer'); ?></div>
            </div>
            <?php
        }
        
        echo '</div>';
        
        echo '<div class="live-indicator">';
        echo '<span class="live-dot"></span>';
        echo __('Actualización en tiempo real', 'videoplayer');
        echo '</div>';
        
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Estadísticas en Vivo', 'videoplayer');
        $show_videos = isset($instance['show_videos']) ? (bool) $instance['show_videos'] : true;
        $show_views = isset($instance['show_views']) ? (bool) $instance['show_views'] : true;
        $show_comments = isset($instance['show_comments']) ? (bool) $instance['show_comments'] : true;
        $show_users = isset($instance['show_users']) ? (bool) $instance['show_users'] : true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p><strong><?php _e('Mostrar estadísticas:', 'videoplayer'); ?></strong></p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_videos); ?> id="<?php echo $this->get_field_id('show_videos'); ?>" name="<?php echo $this->get_field_name('show_videos'); ?>">
            <label for="<?php echo $this->get_field_id('show_videos'); ?>"><?php _e('Total de videos', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_views); ?> id="<?php echo $this->get_field_id('show_views'); ?>" name="<?php echo $this->get_field_name('show_views'); ?>">
            <label for="<?php echo $this->get_field_id('show_views'); ?>"><?php _e('Total de visualizaciones', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_comments); ?> id="<?php echo $this->get_field_id('show_comments'); ?>" name="<?php echo $this->get_field_name('show_comments'); ?>">
            <label for="<?php echo $this->get_field_id('show_comments'); ?>"><?php _e('Total de comentarios', 'videoplayer'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_users); ?> id="<?php echo $this->get_field_id('show_users'); ?>" name="<?php echo $this->get_field_name('show_users'); ?>">
            <label for="<?php echo $this->get_field_id('show_users'); ?>"><?php _e('Usuarios online', 'videoplayer'); ?></label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_videos'] = isset($new_instance['show_videos']) ? (bool) $new_instance['show_videos'] : false;
        $instance['show_views'] = isset($new_instance['show_views']) ? (bool) $new_instance['show_views'] : false;
        $instance['show_comments'] = isset($new_instance['show_comments']) ? (bool) $new_instance['show_comments'] : false;
        $instance['show_users'] = isset($new_instance['show_users']) ? (bool) $new_instance['show_users'] : false;
        
        return $instance;
    }
}

/**
 * Social Media Widget
 */
class VideoPlayer_Social_Media_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'videoplayer_social_media',
            __('🌐 Redes Sociales', 'videoplayer'),
            array(
                'description' => __('Enlaces a redes sociales con iconos', 'videoplayer'),
                'classname' => 'widget-social-media'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $style = isset($instance['style']) ? $instance['style'] : 'icons';
        $target = isset($instance['target']) && $instance['target'] ? '_blank' : '_self';
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $social_networks = array(
            'facebook' => array('label' => 'Facebook', 'icon' => '📘'),
            'twitter' => array('label' => 'Twitter', 'icon' => '🐦'),
            'youtube' => array('label' => 'YouTube', 'icon' => '📺'),
            'instagram' => array('label' => 'Instagram', 'icon' => '📷'),
            'tiktok' => array('label' => 'TikTok', 'icon' => '🎵'),
            'discord' => array('label' => 'Discord', 'icon' => '💬')
        );
        
        echo '<div class="social-media-widget style-' . esc_attr($style) . '">';
        
        foreach ($social_networks as $network => $data) {
            $url = isset($instance[$network . '_url']) ? $instance[$network . '_url'] : '';
            if (!empty($url)) {
                echo '<a href="' . esc_url($url) . '" class="social-link social-' . $network . '" target="' . esc_attr($target) . '" rel="noopener">';
                
                if ($style === 'icons') {
                    echo '<span class="social-icon">' . $data['icon'] . '</span>';
                } else {
                    echo '<span class="social-icon">' . $data['icon'] . '</span>';
                    echo '<span class="social-label">' . $data['label'] . '</span>';
                }
                
                echo '</a>';
            }
        }
        
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Síguenos', 'videoplayer');
        $style = isset($instance['style']) ? $instance['style'] : 'icons';
        $target = isset($instance['target']) ? (bool) $instance['target'] : true;
        
        $social_networks = array(
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'discord' => 'Discord'
        );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'videoplayer'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Estilo:', 'videoplayer'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
                <option value="icons" <?php selected($style, 'icons'); ?>><?php _e('Solo iconos', 'videoplayer'); ?></option>
                <option value="labels" <?php selected($style, 'labels'); ?>><?php _e('Iconos con texto', 'videoplayer'); ?></option>
            </select>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($target); ?> id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>">
            <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Abrir en nueva ventana', 'videoplayer'); ?></label>
        </p>
        
        <hr>
        <p><strong><?php _e('URLs de redes sociales:', 'videoplayer'); ?></strong></p>
        
        <?php foreach ($social_networks as $network => $label) : 
            $url = isset($instance[$network . '_url']) ? $instance[$network . '_url'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id($network . '_url'); ?>"><?php echo $label; ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id($network . '_url'); ?>" name="<?php echo $this->get_field_name($network . '_url'); ?>" type="url" value="<?php echo esc_attr($url); ?>" placeholder="https://">
        </p>
        <?php endforeach; ?>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'icons';
        $instance['target'] = isset($new_instance['target']) ? (bool) $new_instance['target'] : false;
        
        $social_networks = array('facebook', 'twitter', 'youtube', 'instagram', 'tiktok', 'discord');
        
        foreach ($social_networks as $network) {
            $instance[$network . '_url'] = (!empty($new_instance[$network . '_url'])) ? esc_url_raw($new_instance[$network . '_url']) : '';
        }
        
        return $instance;
    }
}

/**
 * Register all widgets
 */
function videoplayer_register_widgets() {
    register_widget('VideoPlayer_Popular_Videos_Widget');
    register_widget('VideoPlayer_Video_Categories_Widget');
    register_widget('VideoPlayer_Live_Stats_Widget');
    register_widget('VideoPlayer_Social_Media_Widget');
}
add_action('widgets_init', 'videoplayer_register_widgets');

/**
 * Register widget areas
 */
function videoplayer_widgets_init() {
    // Main sidebar
    register_sidebar(array(
        'name'          => __('Barra Lateral Principal', 'videoplayer'),
        'id'            => 'sidebar-1',
        'description'   => __('Aparece en la barra lateral principal', 'videoplayer'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Video sidebar (appears on single video pages)
    register_sidebar(array(
        'name'          => __('Barra Lateral de Videos', 'videoplayer'),
        'id'            => 'video-sidebar',
        'description'   => __('Aparece en las páginas individuales de videos', 'videoplayer'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Footer widgets
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(__('Footer Widget %d', 'videoplayer'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(__('Aparece en la columna %d del footer', 'videoplayer'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
    
    // Homepage widgets
    register_sidebar(array(
        'name'          => __('Widgets de la Página Principal', 'videoplayer'),
        'id'            => 'homepage-widgets',
        'description'   => __('Aparece en la página principal', 'videoplayer'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'videoplayer_widgets_init');

/**
 * Enqueue widget styles and scripts
 */
function videoplayer_widget_assets() {
    if (is_active_sidebar('sidebar-1') || is_active_sidebar('video-sidebar')) {
        wp_add_inline_style('videoplayer-style', '
            .widget-popular-videos .popular-video-item:hover {
                transform: translateX(5px);
            }
            
            .widget-video-categories .category-item:hover {
                background: rgba(255, 255, 255, 0.1);
            }
            
            .widget-live-stats .live-dot {
                animation: pulse 2s infinite;
            }
            
            .widget-social-media.style-icons .social-link {
                display: inline-block;
                margin: 0 5px;
                font-size: 1.2em;
            }
            
            .widget-social-media.style-labels .social-link {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
                padding: 8px;
                border-radius: 6px;
                transition: background 0.3s ease;
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'videoplayer_widget_assets', 20);

/**
 * Widget AJAX handlers
 */
function videoplayer_widget_ajax_handlers() {
    // Update live stats
    add_action('wp_ajax_update_live_stats', 'videoplayer_ajax_update_live_stats');
    add_action('wp_ajax_nopriv_update_live_stats', 'videoplayer_ajax_update_live_stats');
}
add_action('init', 'videoplayer_widget_ajax_handlers');

function videoplayer_ajax_update_live_stats() {
    check_ajax_referer('videoplayer_nonce', 'nonce');
    
    $stats = array(
        'videos' => wp_count_posts('video')->publish,
        'comments' => wp_count_comments()->approved,
        'online_users' => rand(20, 100) // Simulate online users
    );
    
    global $wpdb;
    $total_views = $wpdb->get_var("
        SELECT SUM(meta_value) 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_view_count'
    ");
    $stats['views'] = $total_views ?: 0;
    
    wp_send_json_success($stats);
}
?>