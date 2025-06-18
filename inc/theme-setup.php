<?php
/**
 * Theme Setup and Initial Configuration
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup Class
 */
class VideoPlayer_Theme_Setup {
    
    public function __construct() {
        add_action('after_switch_theme', array($this, 'theme_activation'));
        add_action('switch_theme', array($this, 'theme_deactivation'));
        add_action('admin_init', array($this, 'check_requirements'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('wp_loaded', array($this, 'flush_rewrite_rules_maybe'));
    }
    
    /**
     * Theme activation tasks
     */
    public function theme_activation() {
        // Set theme activation flag
        set_transient('videoplayer_theme_activated', true, 30);
        
        // Create default content
        $this->create_default_content();
        
        // Set default customizer options
        $this->set_default_customizer_options();
        
        // Create necessary pages
        $this->create_necessary_pages();
        
        // Setup menus
        $this->setup_default_menus();
        
        // Setup widgets
        $this->setup_default_widgets();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log activation
        error_log('VideoPlayer Mobile Theme: Activated successfully');
    }
    
    /**
     * Theme deactivation tasks
     */
    public function theme_deactivation() {
        // Clean up if needed
        delete_transient('videoplayer_theme_activated');
        
        // Log deactivation
        error_log('VideoPlayer Mobile Theme: Deactivated');
    }
    
    /**
     * Check system requirements
     */
    public function check_requirements() {
        $requirements_met = true;
        $errors = array();
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $requirements_met = false;
            $errors[] = sprintf(
                __('PHP versión 7.4 o superior requerida. Versión actual: %s', 'videoplayer'),
                PHP_VERSION
            );
        }
        
        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '5.0', '<')) {
            $requirements_met = false;
            $errors[] = sprintf(
                __('WordPress versión 5.0 o superior requerida. Versión actual: %s', 'videoplayer'),
                $wp_version
            );
        }
        
        // Check required extensions
        $required_extensions = array('json', 'mbstring', 'curl');
        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $requirements_met = false;
                $errors[] = sprintf(
                    __('Extensión PHP requerida: %s', 'videoplayer'),
                    $extension
                );
            }
        }
        
        // Store results
        if (!$requirements_met) {
            set_transient('videoplayer_requirements_errors', $errors, DAY_IN_SECONDS);
        } else {
            delete_transient('videoplayer_requirements_errors');
        }
    }
    
    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Welcome notice
        if (get_transient('videoplayer_theme_activated')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <h3><?php esc_html_e('¡Bienvenido a VideoPlayer Mobile!', 'videoplayer'); ?></h3>
                <p>
                    <?php esc_html_e('El tema se ha activado correctamente. Para comenzar, visita el', 'videoplayer'); ?>
                    <a href="<?php echo admin_url('customize.php'); ?>"><?php esc_html_e('Customizer', 'videoplayer'); ?></a>
                    <?php esc_html_e('para configurar tu sitio.', 'videoplayer'); ?>
                </p>
                <p>
                    <a href="<?php echo admin_url('post-new.php?post_type=video'); ?>" class="button button-primary">
                        <?php esc_html_e('Crear tu primer video', 'videoplayer'); ?>
                    </a>
                    <a href="<?php echo admin_url('customize.php'); ?>" class="button">
                        <?php esc_html_e('Personalizar tema', 'videoplayer'); ?>
                    </a>
                    <a href="#" class="button" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                        <?php esc_html_e('Cerrar', 'videoplayer'); ?>
                    </a>
                </p>
            </div>
            <?php
            delete_transient('videoplayer_theme_activated');
        }
        
        // Requirements errors
        $errors = get_transient('videoplayer_requirements_errors');
        if ($errors && is_array($errors)) {
            ?>
            <div class="notice notice-error">
                <h3><?php esc_html_e('Requisitos del Sistema No Cumplidos', 'videoplayer'); ?></h3>
                <p><?php esc_html_e('Por favor, corrige los siguientes problemas:', 'videoplayer'); ?></p>
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo esc_html($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }
        
        // Setup reminder
        if (current_user_can('manage_options') && !get_option('videoplayer_setup_completed')) {
            ?>
            <div class="notice notice-info">
                <h3><?php esc_html_e('Completa la Configuración', 'videoplayer'); ?></h3>
                <p>
                    <?php esc_html_e('Para aprovechar al máximo tu tema, completa estos pasos:', 'videoplayer'); ?>
                </p>
                <ol>
                    <li><a href="<?php echo admin_url('customize.php?autofocus[section]=videoplayer_video_settings'); ?>"><?php esc_html_e('Configurar opciones de video', 'videoplayer'); ?></a></li>
                    <li><a href="<?php echo admin_url('customize.php?autofocus[section]=videoplayer_monetization'); ?>"><?php esc_html_e('Configurar monetización', 'videoplayer'); ?></a></li>
                    <li><a href="<?php echo admin_url('nav-menus.php'); ?>"><?php esc_html_e('Crear menús de navegación', 'videoplayer'); ?></a></li>
                    <li><a href="<?php echo admin_url('widgets.php'); ?>"><?php esc_html_e('Configurar widgets', 'videoplayer'); ?></a></li>
                </ol>
                <p>
                    <a href="#" class="button button-primary" onclick="videoplayer_mark_setup_complete(this)">
                        <?php esc_html_e('Marcar como completado', 'videoplayer'); ?>
                    </a>
                </p>
            </div>
            <script>
                function videoplayer_mark_setup_complete(button) {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=videoplayer_mark_setup_complete&nonce=<?php echo wp_create_nonce('videoplayer_setup'); ?>'
                    }).then(() => {
                        button.parentElement.parentElement.style.display = 'none';
                    });
                }
            </script>
            <?php
        }
    }
    
    /**
     * Create default content
     */
    private function create_default_content() {
        // Create sample video posts
        $sample_videos = array(
            array(
                'title' => 'Video de Demostración 1',
                'content' => 'Este es un video de demostración para mostrar las capacidades del tema VideoPlayer Mobile.',
                'duration' => '5:30',
                'view_count' => 1250
            ),
            array(
                'title' => 'Tutorial de Configuración',
                'content' => 'Aprende cómo configurar tu sitio web de videos de manera óptima.',
                'duration' => '8:45',
                'view_count' => 890
            ),
            array(
                'title' => 'Características Móviles',
                'content' => 'Descubre todas las características optimizadas para dispositivos móviles.',
                'duration' => '3:20',
                'view_count' => 2100
            )
        );
        
        foreach ($sample_videos as $video_data) {
            // Check if video already exists
            $existing = get_posts(array(
                'post_type' => 'video',
                'title' => $video_data['title'],
                'post_status' => 'any'
            ));
            
            if (empty($existing)) {
                $post_id = wp_insert_post(array(
                    'post_title' => $video_data['title'],
                    'post_content' => $video_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'video',
                    'post_author' => get_current_user_id()
                ));
                
                if ($post_id) {
                    update_post_meta($post_id, '_video_duration', $video_data['duration']);
                    update_post_meta($post_id, '_view_count', $video_data['view_count']);
                    update_post_meta($post_id, '_redirect_enabled', 1);
                    
                    // Set first video as featured
                    if ($video_data['title'] === 'Video de Demostración 1') {
                        update_post_meta($post_id, '_featured_video', 1);
                    }
                }
            }
        }
    }
    
    /**
     * Set default customizer options
     */
    private function set_default_customizer_options() {
        $default_options = array(
            'primary_color' => '#ff6b6b',
            'secondary_color' => '#4ecdc4',
            'show_search_in_header' => true,
            'sticky_header' => true,
            'autoplay_videos' => false,
            'video_quality' => 'auto',
            'show_video_duration' => true,
            'videos_per_page' => 12,
            'enable_redirects' => true,
            'max_redirects' => 2,
            'show_social_links' => true,
            'enable_lazy_loading' => true,
            'enable_caching' => true,
            'body_font' => 'system',
            'heading_font' => 'system',
            'font_size' => 16
        );
        
        foreach ($default_options as $option => $value) {
            if (get_theme_mod($option) === false) {
                set_theme_mod($option, $value);
            }
        }
    }
    
    /**
     * Create necessary pages
     */
    private function create_necessary_pages() {
        $pages = array(
            'privacy-policy' => array(
                'title' => 'Política de Privacidad',
                'content' => 'Esta página contiene la política de privacidad de nuestro sitio web.'
            ),
            'contact' => array(
                'title' => 'Contacto',
                'content' => 'Ponte en contacto con nosotros a través de este formulario.'
            ),
            'about' => array(
                'title' => 'Acerca de',
                'content' => 'Información acerca de nuestro sitio web y nuestro contenido de videos.'
            )
        );
        
        foreach ($pages as $slug => $page_data) {
            $existing = get_page_by_path($slug);
            
            if (!$existing) {
                wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug,
                    'post_author' => get_current_user_id()
                ));
            }
        }
    }
    
    /**
     * Setup default menus
     */
    private function setup_default_menus() {
        // Get menu locations
        $locations = get_theme_mod('nav_menu_locations');
        
        // Create primary menu if it doesn't exist
        if (empty($locations['primary'])) {
            $menu_name = 'Menú Principal';
            $menu_exists = wp_get_nav_menu_object($menu_name);
            
            if (!$menu_exists) {
                $menu_id = wp_create_nav_menu($menu_name);
                
                if ($menu_id) {
                    // Add menu items
                    $menu_items = array(
                        array(
                            'title' => 'Inicio',
                            'url' => home_url('/'),
                            'menu-item-status' => 'publish'
                        ),
                        array(
                            'title' => 'Videos',
                            'url' => get_post_type_archive_link('video'),
                            'menu-item-status' => 'publish'
                        ),
                        array(
                            'title' => 'Contacto',
                            'url' => home_url('/contact/'),
                            'menu-item-status' => 'publish'
                        )
                    );
                    
                    foreach ($menu_items as $item) {
                        wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => $item['title'],
                            'menu-item-url' => $item['url'],
                            'menu-item-status' => 'publish'
                        ));
                    }
                    
                    // Set menu location
                    $locations['primary'] = $menu_id;
                    $locations['mobile'] = $menu_id; // Use same menu for mobile
                    set_theme_mod('nav_menu_locations', $locations);
                }
            }
        }
    }
    
    /**
     * Setup default widgets
     */
    private function setup_default_widgets() {
        $default_widgets = array(
            'sidebar-1' => array(
                'videoplayer_popular_videos-2' => array(
                    'title' => 'Videos Populares',
                    'number' => 5,
                    'show_views' => true,
                    'show_date' => true
                ),
                'videoplayer_video_categories-2' => array(
                    'title' => 'Categorías',
                    'number' => 6,
                    'show_count' => true,
                    'orderby' => 'count'
                ),
                'videoplayer_live_stats-2' => array(
                    'title' => 'Estadísticas',
                    'show_videos' => true,
                    'show_views' => true,
                    'show_comments' => true,
                    'show_users' => true
                )
            )
        );
        
        // Only set widgets if sidebar is empty
        $existing_widgets = get_option('sidebars_widgets', array());
        
        foreach ($default_widgets as $sidebar_id => $widgets) {
            if (empty($existing_widgets[$sidebar_id])) {
                foreach ($widgets as $widget_id => $widget_data) {
                    $widget_options = get_option('widget_' . explode('-', $widget_id)[0], array());
                    $widget_options[2] = $widget_data;
                    update_option('widget_' . explode('-', $widget_id)[0], $widget_options);
                    
                    $existing_widgets[$sidebar_id][] = $widget_id;
                }
            }
        }
        
        update_option('sidebars_widgets', $existing_widgets);
    }
    
    /**
     * Flush rewrite rules if needed
     */
    public function flush_rewrite_rules_maybe() {
        if (get_transient('videoplayer_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_transient('videoplayer_flush_rewrite_rules');
        }
    }
}

// Initialize theme setup
new VideoPlayer_Theme_Setup();

/**
 * AJAX handler for marking setup as complete
 */
function videoplayer_ajax_mark_setup_complete() {
    check_ajax_referer('videoplayer_setup', 'nonce');
    
    if (current_user_can('manage_options')) {
        update_option('videoplayer_setup_completed', true);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_videoplayer_mark_setup_complete', 'videoplayer_ajax_mark_setup_complete');

/**
 * Reset theme to defaults
 */
function videoplayer_reset_theme() {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    // Remove all theme mods
    remove_theme_mods();
    
    // Reset widgets
    update_option('sidebars_widgets', array());
    
    // Clear caches
    wp_cache_flush();
    
    // Set flag to recreate default content
    set_transient('videoplayer_theme_activated', true, 30);
    
    return true;
}

/**
 * Check if theme is properly configured
 */
function videoplayer_is_configured() {
    $required_settings = array(
        'primary_color',
        'secondary_color',
        'videos_per_page'
    );
    
    foreach ($required_settings as $setting) {
        if (get_theme_mod($setting) === false) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get theme configuration status
 */
function videoplayer_get_config_status() {
    $status = array(
        'theme_configured' => videoplayer_is_configured(),
        'has_videos' => wp_count_posts('video')->publish > 0,
        'has_menus' => !empty(get_nav_menu_locations()),
        'has_widgets' => !empty(get_option('sidebars_widgets', array())['sidebar-1']),
        'requirements_met' => !get_transient('videoplayer_requirements_errors')
    );
    
    $status['overall_complete'] = array_sum($status) === count($status);
    
    return $status;
}

/**
 * Export theme configuration
 */
function videoplayer_export_config() {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $config = array(
        'theme_mods' => get_theme_mods(),
        'widgets' => get_option('sidebars_widgets'),
        'menus' => get_nav_menu_locations(),
        'options' => array(
            'videoplayer_settings' => get_option('videoplayer_settings')
        ),
        'timestamp' => current_time('mysql'),
        'version' => wp_get_theme()->get('Version')
    );
    
    return json_encode($config, JSON_PRETTY_PRINT);
}

/**
 * Import theme configuration
 */
function videoplayer_import_config($config_json) {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $config = json_decode($config_json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return new WP_Error('invalid_json', 'Invalid JSON configuration');
    }
    
    // Import theme mods
    if (isset($config['theme_mods'])) {
        foreach ($config['theme_mods'] as $mod => $value) {
            set_theme_mod($mod, $value);
        }
    }
    
    // Import widgets
    if (isset($config['widgets'])) {
        update_option('sidebars_widgets', $config['widgets']);
    }
    
    // Import options
    if (isset($config['options'])) {
        foreach ($config['options'] as $option => $value) {
            update_option($option, $value);
        }
    }
    
    return true;
}
?>