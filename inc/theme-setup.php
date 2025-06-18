<?php
/**
 * Theme Setup Functions for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Track video views on single video pages
 */
function videoplayer_track_video_view() {
    if (is_singular('video')) {
        $post_id = get_the_ID();
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $view_key = 'video_view_' . $post_id . '_' . md5($user_ip);
        
        // Only count one view per IP per day
        if (!get_transient($view_key)) {
            update_video_views($post_id);
            set_transient($view_key, true, DAY_IN_SECONDS);
        }
    }
}
add_action('wp_head', 'videoplayer_track_video_view');

/**
 * Custom excerpt length
 */
function videoplayer_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'videoplayer_excerpt_length');

/**
 * Custom excerpt more
 */
function videoplayer_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'videoplayer_excerpt_more');

/**
 * Add body classes
 */
function videoplayer_body_classes($classes) {
    // Add mobile class
    if (wp_is_mobile()) {
        $classes[] = 'mobile-device';
    }
    
    // Add video class on video pages
    if (is_singular('video')) {
        $classes[] = 'single-video-page';
    }
    
    // Add search class
    if (is_search()) {
        $classes[] = 'search-results-page';
    }
    
    return $classes;
}
add_filter('body_class', 'videoplayer_body_classes');

/**
 * Modify main query for video archives
 */
function videoplayer_modify_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home()) {
            $query->set('post_type', array('post', 'video'));
        }
        
        if (is_post_type_archive('video')) {
            $posts_per_page = get_theme_mod('videos_per_page', 12);
            $query->set('posts_per_page', $posts_per_page);
            
            // Handle sorting
            if (isset($_GET['orderby'])) {
                switch ($_GET['orderby']) {
                    case 'popular':
                        $query->set('meta_key', '_view_count');
                        $query->set('orderby', 'meta_value_num');
                        $query->set('order', 'DESC');
                        break;
                    case 'duration':
                        $query->set('meta_key', '_video_duration');
                        $query->set('orderby', 'meta_value');
                        break;
                    case 'title':
                        $query->set('orderby', 'title');
                        $query->set('order', 'ASC');
                        break;
                }
            }
        }
    }
}
add_action('pre_get_posts', 'videoplayer_modify_main_query');

/**
 * Add manifest link
 */
function videoplayer_add_manifest() {
    echo '<link rel="manifest" href="' . get_template_directory_uri() . '/manifest.json">';
    echo '<meta name="theme-color" content="' . get_theme_mod('primary_color', '#ff6b6b') . '">';
}
add_action('wp_head', 'videoplayer_add_manifest');

/**
 * Contact form handler
 */
function videoplayer_handle_contact_form() {
    if (isset($_POST['action']) && $_POST['action'] === 'submit_contact_form') {
        if (!wp_verify_nonce($_POST['contact_nonce'], 'contact_form_nonce')) {
            wp_die('Security check failed');
        }
        
        $name = sanitize_text_field($_POST['contact_name']);
        $email = sanitize_email($_POST['contact_email']);
        $subject = sanitize_text_field($_POST['contact_subject']);
        $message = sanitize_textarea_field($_POST['contact_message']);
        
        $to = get_option('admin_email');
        $email_subject = 'Nuevo mensaje de contacto: ' . $subject;
        $email_message = "Nombre: $name\n";
        $email_message .= "Email: $email\n\n";
        $email_message .= "Mensaje:\n$message";
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        if (wp_mail($to, $email_subject, $email_message, $headers)) {
            wp_redirect(add_query_arg('contact', 'success', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('contact', 'error', wp_get_referer()));
        }
        exit;
    }
}
add_action('admin_post_submit_contact_form', 'videoplayer_handle_contact_form');
add_action('admin_post_nopriv_submit_contact_form', 'videoplayer_handle_contact_form');

/**
 * Custom login styles
 */
function videoplayer_login_styles() {
    ?>
    <style type="text/css">
        body.login {
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
        }
        
        .login h1 a {
            background-image: none;
            background-color: #ff6b6b;
            color: white;
            width: auto;
            height: auto;
            text-indent: 0;
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
            border-radius: 8px;
        }
        
        .login form {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login label {
            color: #ffffff;
        }
        
        .login input[type="text"],
        .login input[type="password"] {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        
        .wp-core-ui .button-primary {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border: none;
            text-shadow: none;
            box-shadow: none;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'videoplayer_login_styles');

/**
 * Remove unnecessary scripts and styles
 */
function videoplayer_dequeue_scripts() {
    if (!is_admin()) {
        // Remove emoji scripts
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        
        // Remove block library CSS if not using Gutenberg
        if (!current_theme_supports('wp-block-styles')) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
        }
    }
}
add_action('wp_enqueue_scripts', 'videoplayer_dequeue_scripts', 100);

/**
 * Security enhancements
 */
// Remove WordPress version
remove_action('wp_head', 'wp_generator');

// Remove RSD link
remove_action('wp_head', 'rsd_link');

// Remove wlwmanifest link
remove_action('wp_head', 'wlwmanifest_link');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Performance optimizations
 */
// Defer parsing of JavaScript
function videoplayer_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('videoplayer-main', 'videoplayer-video');
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'videoplayer_defer_scripts', 10, 3);

// Add preload hints
function videoplayer_preload_hints() {
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/js/main.js" as="script">';
    echo '<link rel="prefetch" href="' . get_template_directory_uri() . '/js/video-player.js">';
}
add_action('wp_head', 'videoplayer_preload_hints', 1);

/**
 * AJAX handler for load more videos
 */
function videoplayer_ajax_load_more_videos() {
    check_ajax_referer('videoplayer_nonce', 'nonce');
    
    $page = intval($_POST['page']);
    $posts_per_page = intval($_POST['posts_per_page']);
    
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    $videos = new WP_Query($args);
    
    if ($videos->have_posts()) {
        ob_start();
        while ($videos->have_posts()) {
            $videos->the_post();
            get_template_part('template-parts/video-card');
        }
        $html = ob_get_clean();
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'html' => $html,
            'found_posts' => $videos->found_posts,
            'max_pages' => $videos->max_num_pages
        ));
    } else {
        wp_send_json_error('No more videos found');
    }
}
add_action('wp_ajax_load_more_videos', 'videoplayer_ajax_load_more_videos');
add_action('wp_ajax_nopriv_load_more_videos', 'videoplayer_ajax_load_more_videos');

/**
 * Calculate estimated reading time
 */
function videoplayer_estimated_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    
    return max(1, $reading_time);
}

/**
 * Add theme support for various WordPress features
 */
function videoplayer_after_setup_theme() {
    // Add support for post formats
    add_theme_support('post-formats', array(
        'video',
        'audio',
        'gallery',
        'quote',
        'link'
    ));
}
add_action('after_setup_theme', 'videoplayer_after_setup_theme');

/**
 * Enqueue admin styles
 */
function videoplayer_admin_styles() {
    wp_enqueue_style(
        'videoplayer-admin',
        get_template_directory_uri() . '/admin-style.css',
        array(),
        VIDEOPLAYER_VERSION
    );
}
add_action('admin_enqueue_scripts', 'videoplayer_admin_styles');

/**
 * Add custom image sizes info
 */
function videoplayer_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'video-thumbnail' => __('Video Miniatura', 'videoplayer'),
        'video-large' => __('Video Grande', 'videoplayer'),
        'video-hero' => __('Video Hero', 'videoplayer'),
    ));
}
add_filter('image_size_names_choose', 'videoplayer_custom_image_sizes');

/**
 * Custom admin footer text
 */
function videoplayer_admin_footer_text($footer_text) {
    $screen = get_current_screen();
    
    if (strpos($screen->id, 'video') !== false || $screen->id === 'themes') {
        $footer_text = sprintf(
            __('¿Te gusta VideoPlayer Mobile? %1$sPor favor califícanos%2$s en WordPress.org', 'videoplayer'),
            '<a href="https://wordpress.org/themes/videoplayer-mobile/" target="_blank">',
            '</a>'
        );
    }
    
    return $footer_text;
}
add_filter('admin_footer_text', 'videoplayer_admin_footer_text');

/**
 * Add dashboard widget
 */
function videoplayer_dashboard_widget() {
    wp_add_dashboard_widget(
        'videoplayer_stats',
        __('Estadísticas de Videos', 'videoplayer'),
        'videoplayer_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'videoplayer_dashboard_widget');

function videoplayer_dashboard_widget_content() {
    $total_videos = wp_count_posts('video');
    $total_views = 0;
    
    // Calculate total views
    $videos = get_posts(array(
        'post_type' => 'video',
        'posts_per_page' => -1,
        'meta_key' => '_view_count'
    ));
    
    foreach ($videos as $video) {
        $views = get_post_meta($video->ID, '_view_count', true);
        $total_views += intval($views);
    }
    
    ?>
    <div class="videoplayer-dashboard-stats">
        <div class="stat-item">
            <span class="stat-number"><?php echo $total_videos->publish; ?></span>
            <span class="stat-label"><?php esc_html_e('Videos Publicados', 'videoplayer'); ?></span>
        </div>
        
        <div class="stat-item">
            <span class="stat-number"><?php echo number_format($total_views); ?></span>
            <span class="stat-label"><?php esc_html_e('Vistas Totales', 'videoplayer'); ?></span>
        </div>
        
        <div class="stat-item">
            <span class="stat-number"><?php echo $total_videos->draft; ?></span>
            <span class="stat-label"><?php esc_html_e('Borradores', 'videoplayer'); ?></span>
        </div>
        
        <p>
            <a href="<?php echo admin_url('edit.php?post_type=video'); ?>" class="button button-primary">
                <?php esc_html_e('Gestionar Videos', 'videoplayer'); ?>
            </a>
        </p>
    </div>
    
    <style>
    .videoplayer-dashboard-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        text-align: center;
        flex: 1;
        min-width: 80px;
    }
    
    .stat-number {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color: #0073aa;
    }
    
    .stat-label {
        font-size: 12px;
        color: #666;
    }
    </style>
    <?php
}