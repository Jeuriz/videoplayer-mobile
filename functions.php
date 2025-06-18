<?php
/**
 * VideoPlayer Mobile Theme Functions
 * 
 * @package VideoPlayerMobile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('VIDEOPLAYER_VERSION', '1.0.0');
define('VIDEOPLAYER_THEME_DIR', get_template_directory());
define('VIDEOPLAYER_THEME_URL', get_template_directory_uri());

/**
 * Theme setup
 */
function videoplayer_setup() {
    // Make theme available for translation
    load_theme_textdomain('videoplayer', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Custom image sizes for videos
    add_image_size('video-thumbnail', 320, 180, true);
    add_image_size('video-large', 800, 450, true);
    add_image_size('video-hero', 1200, 675, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'videoplayer'),
        'mobile' => __('Menú Móvil', 'videoplayer'),
        'footer' => __('Menú Footer', 'videoplayer'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add support for core custom logo
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');
    
    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for custom background
    add_theme_support('custom-background', array(
        'default-color' => '0c0c0c',
    ));

    // Add support for wide alignment
    add_theme_support('align-wide');

    // Set content width
    if (!isset($content_width)) {
        $content_width = 800;
    }
}
add_action('after_setup_theme', 'videoplayer_setup');

/**
 * Enqueue scripts and styles
 */
function videoplayer_scripts() {
    // Theme stylesheet
    wp_enqueue_style(
        'videoplayer-style',
        get_stylesheet_uri(),
        array(),
        VIDEOPLAYER_VERSION
    );

    // Main JavaScript
    wp_enqueue_script(
        'videoplayer-main',
        VIDEOPLAYER_THEME_URL . '/js/main.js',
        array('jquery'),
        VIDEOPLAYER_VERSION,
        true
    );

    // Video player scripts
    wp_enqueue_script(
        'videoplayer-video',
        VIDEOPLAYER_THEME_URL . '/js/video-player.js',
        array('jquery'),
        VIDEOPLAYER_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('videoplayer-main', 'videoPlayerAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('videoplayer_nonce'),
        'translations' => array(
            'loading' => __('Cargando...', 'videoplayer'),
            'error' => __('Error al cargar contenido', 'videoplayer'),
            'noMoreResults' => __('No hay más resultados', 'videoplayer'),
        )
    ));

    // Pass theme config to JavaScript
    wp_localize_script('videoplayer-main', 'videoPlayerConfig', array(
        'redirectUrl' => get_theme_mod('redirect_url', ''),
        'maxRedirects' => get_theme_mod('max_redirects', 2),
        'redirectEnabled' => get_theme_mod('enable_redirects', true),
        'autoplayVideos' => get_theme_mod('autoplay_videos', false),
        'lazyLoading' => get_theme_mod('enable_lazy_loading', true),
        'debug' => defined('WP_DEBUG') && WP_DEBUG,
    ));

    // Conditional scripts
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Mobile detection
    wp_add_inline_script('videoplayer-main', '
        document.documentElement.classList.add(
            /Mobi|Android/i.test(navigator.userAgent) ? "mobile" : "desktop"
        );
    ');
}
add_action('wp_enqueue_scripts', 'videoplayer_scripts');

/**
 * Include required files
 */
require_once VIDEOPLAYER_THEME_DIR . '/inc/post-types.php';
require_once VIDEOPLAYER_THEME_DIR . '/inc/video-functions.php';
require_once VIDEOPLAYER_THEME_DIR . '/inc/customizer.php';
require_once VIDEOPLAYER_THEME_DIR . '/inc/widgets.php';
require_once VIDEOPLAYER_THEME_DIR . '/inc/theme-setup.php';

/**
 * Video custom post type
 */
function videoplayer_register_video_post_type() {
    $labels = array(
        'name' => __('Videos', 'videoplayer'),
        'singular_name' => __('Video', 'videoplayer'),
        'menu_name' => __('Videos', 'videoplayer'),
        'add_new' => __('Agregar Nuevo', 'videoplayer'),
        'add_new_item' => __('Agregar Nuevo Video', 'videoplayer'),
        'edit_item' => __('Editar Video', 'videoplayer'),
        'new_item' => __('Nuevo Video', 'videoplayer'),
        'view_item' => __('Ver Video', 'videoplayer'),
        'search_items' => __('Buscar Videos', 'videoplayer'),
        'not_found' => __('No se encontraron videos', 'videoplayer'),
        'not_found_in_trash' => __('No se encontraron videos en la papelera', 'videoplayer'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'videos'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
        'menu_icon' => 'dashicons-video-alt3',
        'show_in_rest' => true,
        'taxonomies' => array('category', 'post_tag'),
    );

    register_post_type('video', $args);
}
add_action('init', 'videoplayer_register_video_post_type');

/**
 * Video helper functions
 */
function get_video_duration($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $duration = get_post_meta($post_id, '_video_duration', true);
    return $duration ? $duration : '0:00';
}

function get_video_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $views = get_post_meta($post_id, '_view_count', true);
    return $views ? intval($views) : 0;
}

function update_video_views($post_id) {
    $views = get_video_views($post_id);
    update_post_meta($post_id, '_view_count', $views + 1);
}

function is_redirect_enabled($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_redirect_enabled', true) == '1';
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
 * Add video meta boxes
 */
function videoplayer_add_video_meta_boxes() {
    add_meta_box(
        'video-details',
        __('Detalles del Video', 'videoplayer'),
        'videoplayer_video_meta_box_callback',
        'video',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'videoplayer_add_video_meta_boxes');

function videoplayer_video_meta_box_callback($post) {
    wp_nonce_field('videoplayer_save_video_meta', 'videoplayer_video_meta_nonce');
    
    $duration = get_post_meta($post->ID, '_video_duration', true);
    $video_url = get_post_meta($post->ID, '_video_url', true);
    $redirect_enabled = get_post_meta($post->ID, '_redirect_enabled', true);
    $featured_video = get_post_meta($post->ID, '_featured_video', true);
    
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="video_duration">' . __('Duración del Video', 'videoplayer') . '</label></th>';
    echo '<td><input type="text" id="video_duration" name="video_duration" value="' . esc_attr($duration) . '" placeholder="5:30" /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="video_url">' . __('URL del Video', 'videoplayer') . '</label></th>';
    echo '<td><input type="url" id="video_url" name="video_url" value="' . esc_attr($video_url) . '" class="large-text" /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="redirect_enabled">' . __('Habilitar Redirección', 'videoplayer') . '</label></th>';
    echo '<td><input type="checkbox" id="redirect_enabled" name="redirect_enabled" value="1" ' . checked($redirect_enabled, '1', false) . ' /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="featured_video">' . __('Video Destacado', 'videoplayer') . '</label></th>';
    echo '<td><input type="checkbox" id="featured_video" name="featured_video" value="1" ' . checked($featured_video, '1', false) . ' /></td>';
    echo '</tr>';
    echo '</table>';
}

function videoplayer_save_video_meta($post_id) {
    if (!isset($_POST['videoplayer_video_meta_nonce']) || 
        !wp_verify_nonce($_POST['videoplayer_video_meta_nonce'], 'videoplayer_save_video_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('video_duration', 'video_url', 'redirect_enabled', 'featured_video');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }
}
add_action('save_post', 'videoplayer_save_video_meta');

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
 * AJAX handlers
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