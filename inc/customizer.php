<?php
/**
 * Theme Customizer Configuration
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add customizer support
 */
function videoplayer_customize_register($wp_customize) {
    
    // Add custom colors
    $wp_customize->add_setting('primary_color', array(
        'default' => '#ff6b6b',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage'
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Color Primario', 'videoplayer'),
        'section' => 'colors',
        'settings' => 'primary_color'
    )));
    
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#4ecdc4',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage'
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Color Secundario', 'videoplayer'),
        'section' => 'colors',
        'settings' => 'secondary_color'
    )));

    // Theme Options Section
    $wp_customize->add_section('videoplayer_theme_options', array(
        'title' => __('Opciones del Tema', 'videoplayer'),
        'priority' => 30,
        'description' => __('Personaliza la configuración general del tema.', 'videoplayer')
    ));

    // Logo Settings
    $wp_customize->add_setting('custom_logo_text', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage'
    ));
    
    $wp_customize->add_control('custom_logo_text', array(
        'label' => __('Texto del Logo', 'videoplayer'),
        'section' => 'title_tagline',
        'type' => 'text',
        'priority' => 10
    ));

    // Header Settings
    $wp_customize->add_section('videoplayer_header', array(
        'title' => __('Configuración del Header', 'videoplayer'),
        'priority' => 35
    ));

    $wp_customize->add_setting('show_search_in_header', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('show_search_in_header', array(
        'label' => __('Mostrar búsqueda en el header', 'videoplayer'),
        'section' => 'videoplayer_header',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('sticky_header', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('sticky_header', array(
        'label' => __('Header fijo al hacer scroll', 'videoplayer'),
        'section' => 'videoplayer_header',
        'type' => 'checkbox'
    ));

    // Video Settings Section
    $wp_customize->add_section('videoplayer_video_settings', array(
        'title' => __('Configuración de Videos', 'videoplayer'),
        'priority' => 40,
        'description' => __('Configuración específica para el reproductor de videos.', 'videoplayer')
    ));

    $wp_customize->add_setting('autoplay_videos', array(
        'default' => false,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('autoplay_videos', array(
        'label' => __('Reproducción automática', 'videoplayer'),
        'description' => __('Los videos se reproducirán automáticamente (solo con sonido silenciado)', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('video_quality', array(
        'default' => 'auto',
        'sanitize_callback' => 'videoplayer_sanitize_select'
    ));
    
    $wp_customize->add_control('video_quality', array(
        'label' => __('Calidad de video por defecto', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'select',
        'choices' => array(
            'auto' => __('Automática', 'videoplayer'),
            '1080p' => __('1080p (Full HD)', 'videoplayer'),
            '720p' => __('720p (HD)', 'videoplayer'),
            '480p' => __('480p (SD)', 'videoplayer')
        )
    ));

    $wp_customize->add_setting('show_video_duration', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('show_video_duration', array(
        'label' => __('Mostrar duración en miniaturas', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('videos_per_page', array(
        'default' => 12,
        'sanitize_callback' => 'absint'
    ));
    
    $wp_customize->add_control('videos_per_page', array(
        'label' => __('Videos por página', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 6,
            'max' => 24,
            'step' => 3
        )
    ));

    // Monetization Settings
    $wp_customize->add_section('videoplayer_monetization', array(
        'title' => __('Configuración de Monetización', 'videoplayer'),
        'priority' => 45,
        'description' => __('Configuración para la monetización y redirecciones.', 'videoplayer')
    ));

    $wp_customize->add_setting('enable_redirects', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('enable_redirects', array(
        'label' => __('Habilitar sistema de redirecciones', 'videoplayer'),
        'section' => 'videoplayer_monetization',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('redirect_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('redirect_url', array(
        'label' => __('URL de redirección', 'videoplayer'),
        'section' => 'videoplayer_monetization',
        'type' => 'url'
    ));

    $wp_customize->add_setting('max_redirects', array(
        'default' => 2,
        'sanitize_callback' => 'absint'
    ));
    
    $wp_customize->add_control('max_redirects', array(
        'label' => __('Máximo de redirecciones por sesión', 'videoplayer'),
        'section' => 'videoplayer_monetization',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10
        )
    ));

    $wp_customize->add_setting('ads_script_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('ads_script_url', array(
        'label' => __('URL del script de publicidad', 'videoplayer'),
        'section' => 'videoplayer_monetization',
        'type' => 'url'
    ));

    // Social Media Section
    $wp_customize->add_section('videoplayer_social_media', array(
        'title' => __('Redes Sociales', 'videoplayer'),
        'priority' => 50,
        'description' => __('Enlaces a tus redes sociales.', 'videoplayer')
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'youtube' => 'YouTube',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'discord' => 'Discord'
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting($network . '_url', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ));
        
        $wp_customize->add_control($network . '_url', array(
            'label' => $label . ' URL',
            'section' => 'videoplayer_social_media',
            'type' => 'url'
        ));
    }

    $wp_customize->add_setting('show_social_links', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('show_social_links', array(
        'label' => __('Mostrar enlaces sociales en el footer', 'videoplayer'),
        'section' => 'videoplayer_social_media',
        'type' => 'checkbox'
    ));

    // Performance Section
    $wp_customize->add_section('videoplayer_performance', array(
        'title' => __('Rendimiento', 'videoplayer'),
        'priority' => 55,
        'description' => __('Configuración para optimizar el rendimiento del sitio.', 'videoplayer')
    ));

    $wp_customize->add_setting('enable_lazy_loading', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('enable_lazy_loading', array(
        'label' => __('Carga diferida de imágenes', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('minify_css', array(
        'default' => false,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('minify_css', array(
        'label' => __('Minificar CSS', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox'
    ));

    $wp_customize->add_setting('enable_caching', array(
        'default' => true,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('enable_caching', array(
        'label' => __('Habilitar caché del navegador', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox'
    ));

    // Typography Section
    $wp_customize->add_section('videoplayer_typography', array(
        'title' => __('Tipografía', 'videoplayer'),
        'priority' => 60
    ));

    $wp_customize->add_setting('body_font', array(
        'default' => 'system',
        'sanitize_callback' => 'videoplayer_sanitize_select'
    ));
    
    $wp_customize->add_control('body_font', array(
        'label' => __('Fuente del cuerpo', 'videoplayer'),
        'section' => 'videoplayer_typography',
        'type' => 'select',
        'choices' => array(
            'system' => __('Fuente del sistema', 'videoplayer'),
            'roboto' => 'Roboto',
            'open-sans' => 'Open Sans',
            'lato' => 'Lato',
            'montserrat' => 'Montserrat'
        )
    ));

    $wp_customize->add_setting('heading_font', array(
        'default' => 'system',
        'sanitize_callback' => 'videoplayer_sanitize_select'
    ));
    
    $wp_customize->add_control('heading_font', array(
        'label' => __('Fuente de los títulos', 'videoplayer'),
        'section' => 'videoplayer_typography',
        'type' => 'select',
        'choices' => array(
            'system' => __('Fuente del sistema', 'videoplayer'),
            'roboto' => 'Roboto',
            'open-sans' => 'Open Sans',
            'lato' => 'Lato',
            'montserrat' => 'Montserrat',
            'poppins' => 'Poppins'
        )
    ));

    $wp_customize->add_setting('font_size', array(
        'default' => '16',
        'sanitize_callback' => 'absint'
    ));
    
    $wp_customize->add_control('font_size', array(
        'label' => __('Tamaño de fuente base (px)', 'videoplayer'),
        'section' => 'videoplayer_typography',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 14,
            'max' => 20
        )
    ));

    // Advanced Section
    $wp_customize->add_section('videoplayer_advanced', array(
        'title' => __('Configuración Avanzada', 'videoplayer'),
        'priority' => 70,
        'description' => __('Opciones avanzadas para usuarios experimentados.', 'videoplayer')
    ));

    $wp_customize->add_setting('custom_css', array(
        'default' => '',
        'sanitize_callback' => 'videoplayer_sanitize_css'
    ));
    
    $wp_customize->add_control('custom_css', array(
        'label' => __('CSS Personalizado', 'videoplayer'),
        'section' => 'videoplayer_advanced',
        'type' => 'textarea',
        'input_attrs' => array(
            'rows' => 10,
            'placeholder' => '/* Escribe tu CSS personalizado aquí */'
        )
    ));

    $wp_customize->add_setting('custom_js', array(
        'default' => '',
        'sanitize_callback' => 'videoplayer_sanitize_js'
    ));
    
    $wp_customize->add_control('custom_js', array(
        'label' => __('JavaScript Personalizado', 'videoplayer'),
        'section' => 'videoplayer_advanced',
        'type' => 'textarea',
        'input_attrs' => array(
            'rows' => 10,
            'placeholder' => '// Escribe tu JavaScript personalizado aquí'
        )
    ));

    $wp_customize->add_setting('enable_debug_mode', array(
        'default' => false,
        'sanitize_callback' => 'videoplayer_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('enable_debug_mode', array(
        'label' => __('Modo debug', 'videoplayer'),
        'description' => __('Habilita información de depuración en la consola', 'videoplayer'),
        'section' => 'videoplayer_advanced',
        'type' => 'checkbox'
    ));

    // Add selective refresh support
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('custom_logo_text', array(
            'selector' => '.site-logo',
            'render_callback' => 'videoplayer_customize_partial_logo'
        ));
        
        $wp_customize->selective_refresh->add_partial('blogdescription', array(
            'selector' => '.site-description',
            'render_callback' => 'videoplayer_customize_partial_description'
        ));
    }
}
add_action('customize_register', 'videoplayer_customize_register');

/**
 * Sanitization functions
 */
function videoplayer_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

function videoplayer_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

function videoplayer_sanitize_css($css) {
    return wp_strip_all_tags($css);
}

function videoplayer_sanitize_js($js) {
    return wp_strip_all_tags($js);
}

/**
 * Partial refresh functions
 */
function videoplayer_customize_partial_logo() {
    $logo_text = get_theme_mod('custom_logo_text', get_bloginfo('name'));
    return esc_html($logo_text);
}

function videoplayer_customize_partial_description() {
    return get_bloginfo('description');
}

/**
 * Enqueue customizer scripts
 */
function videoplayer_customize_preview_js() {
    wp_enqueue_script(
        'videoplayer-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
}
add_action('customize_preview_init', 'videoplayer_customize_preview_js');

/**
 * Output custom CSS
 */
function videoplayer_customizer_css() {
    $primary_color = get_theme_mod('primary_color', '#ff6b6b');
    $secondary_color = get_theme_mod('secondary_color', '#4ecdc4');
    $font_size = get_theme_mod('font_size', '16');
    $custom_css = get_theme_mod('custom_css', '');
    
    $css = "
        :root {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
            --gradient-primary: linear-gradient(45deg, {$primary_color}, {$secondary_color});
        }
        
        html {
            font-size: {$font_size}px;
        }
    ";
    
    // Add font family CSS
    $body_font = get_theme_mod('body_font', 'system');
    $heading_font = get_theme_mod('heading_font', 'system');
    
    if ($body_font !== 'system') {
        $css .= "body { font-family: '{$body_font}', sans-serif; }";
    }
    
    if ($heading_font !== 'system') {
        $css .= "h1, h2, h3, h4, h5, h6 { font-family: '{$heading_font}', sans-serif; }";
    }
    
    // Add custom CSS
    if ($custom_css) {
        $css .= $custom_css;
    }
    
    if (!empty($css)) {
        echo '<style type="text/css" id="videoplayer-customizer-css">' . $css . '</style>';
    }
}
add_action('wp_head', 'videoplayer_customizer_css');

/**
 * Output custom JavaScript
 */
function videoplayer_customizer_js() {
    $custom_js = get_theme_mod('custom_js', '');
    $debug_mode = get_theme_mod('enable_debug_mode', false);
    
    if ($custom_js || $debug_mode) {
        echo '<script type="text/javascript">';
        
        if ($debug_mode) {
            echo 'window.videoPlayerDebug = true;';
        }
        
        if ($custom_js) {
            echo $custom_js;
        }
        
        echo '</script>';
    }
}
add_action('wp_footer', 'videoplayer_customizer_js');

/**
 * Enqueue Google Fonts
 */
function videoplayer_google_fonts() {
    $body_font = get_theme_mod('body_font', 'system');
    $heading_font = get_theme_mod('heading_font', 'system');
    
    $fonts = array();
    
    if ($body_font !== 'system' && $body_font !== $heading_font) {
        $fonts[] = $body_font . ':300,400,500,600,700';
    }
    
    if ($heading_font !== 'system') {
        $fonts[] = $heading_font . ':400,500,600,700,800';
    }
    
    if (!empty($fonts)) {
        $fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $fonts) . '&display=swap';
        wp_enqueue_style('videoplayer-google-fonts', $fonts_url, array(), null);
    }
}
add_action('wp_enqueue_scripts', 'videoplayer_google_fonts');

/**
 * Add customizer controls styles
 */
function videoplayer_customizer_controls_css() {
    ?>
    <style>
        .customize-control-description {
            font-style: italic;
            font-size: 12px;
            margin-top: 5px;
        }
        
        #customize-control-primary_color .wp-color-picker,
        #customize-control-secondary_color .wp-color-picker {
            border-radius: 4px;
        }
        
        .customize-section-description {
            background: #f7f7f7;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #0073aa;
        }
    </style>
    <?php
}
add_action('customize_controls_print_styles', 'videoplayer_customizer_controls_css');

/**
 * Register customizer panels for better organization
 */
function videoplayer_customizer_panels($wp_customize) {
    // Theme Options Panel
    $wp_customize->add_panel('videoplayer_theme_panel', array(
        'title' => __('VideoPlayer Tema', 'videoplayer'),
        'description' => __('Configuración completa del tema VideoPlayer Mobile', 'videoplayer'),
        'priority' => 20
    ));
    
    // Move sections to panel
    $wp_customize->get_section('videoplayer_theme_options')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_header')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_video_settings')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_monetization')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_social_media')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_performance')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_typography')->panel = 'videoplayer_theme_panel';
    $wp_customize->get_section('videoplayer_advanced')->panel = 'videoplayer_theme_panel';
}
add_action('customize_register', 'videoplayer_customizer_panels', 15);
?>