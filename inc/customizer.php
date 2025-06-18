<?php
/**
 * VideoPlayer Mobile Theme Customizer
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 */
function videoplayer_customize_register($wp_customize) {
    
    // Add postMessage support for default WordPress settings
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    // Remove default color scheme control
    $wp_customize->remove_control('header_textcolor');

    /**
     * Theme Colors Section
     */
    $wp_customize->add_section('videoplayer_colors', array(
        'title' => __('Colores del Tema', 'videoplayer'),
        'priority' => 30,
        'description' => __('Personaliza los colores principales del tema.', 'videoplayer'),
    ));

    // Primary Color
    $wp_customize->add_setting('primary_color', array(
        'default' => '#ff6b6b',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Color Primario', 'videoplayer'),
        'description' => __('Color principal usado en botones, enlaces y elementos destacados.', 'videoplayer'),
        'section' => 'videoplayer_colors',
        'settings' => 'primary_color',
    )));

    // Secondary Color
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#4ecdc4',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Color Secundario', 'videoplayer'),
        'description' => __('Color complementario usado en gradientes y efectos.', 'videoplayer'),
        'section' => 'videoplayer_colors',
        'settings' => 'secondary_color',
    )));

    // Background Color
    $wp_customize->add_setting('background_color', array(
        'default' => '#0c0c0c',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
        'label' => __('Color de Fondo', 'videoplayer'),
        'description' => __('Color de fondo principal del sitio.', 'videoplayer'),
        'section' => 'videoplayer_colors',
        'settings' => 'background_color',
    )));

    // Text Color
    $wp_customize->add_setting('text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'text_color', array(
        'label' => __('Color de Texto', 'videoplayer'),
        'description' => __('Color principal del texto.', 'videoplayer'),
        'section' => 'videoplayer_colors',
        'settings' => 'text_color',
    )));

    /**
     * Video Settings Section
     */
    $wp_customize->add_section('videoplayer_video_settings', array(
        'title' => __('Configuración de Videos', 'videoplayer'),
        'priority' => 40,
        'description' => __('Ajustes relacionados con la reproducción y visualización de videos.', 'videoplayer'),
    ));

    // Videos per page
    $wp_customize->add_setting('videos_per_page', array(
        'default' => 12,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('videos_per_page', array(
        'label' => __('Videos por Página', 'videoplayer'),
        'description' => __('Número de videos a mostrar en las páginas de archivo.', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 50,
        ),
    ));

    // Autoplay videos
    $wp_customize->add_setting('autoplay_videos', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('autoplay_videos', array(
        'label' => __('Reproducción Automática', 'videoplayer'),
        'description' => __('Reproducir videos automáticamente (silenciados).', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox',
    ));

    // Show video duration
    $wp_customize->add_setting('show_video_duration', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_video_duration', array(
        'label' => __('Mostrar Duración', 'videoplayer'),
        'description' => __('Mostrar la duración del video en las miniaturas.', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox',
    ));

    // Show view count
    $wp_customize->add_setting('show_view_count', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_view_count', array(
        'label' => __('Mostrar Contador de Vistas', 'videoplayer'),
        'description' => __('Mostrar el número de visualizaciones en los videos.', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox',
    ));

    // Enable lazy loading
    $wp_customize->add_setting('enable_lazy_loading', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_lazy_loading', array(
        'label' => __('Carga Perezosa', 'videoplayer'),
        'description' => __('Cargar videos solo cuando sean visibles.', 'videoplayer'),
        'section' => 'videoplayer_video_settings',
        'type' => 'checkbox',
    ));

    /**
     * Redirect Settings Section
     */
    $wp_customize->add_section('videoplayer_redirect_settings', array(
        'title' => __('Sistema de Redirección', 'videoplayer'),
        'priority' => 50,
        'description' => __('Configuración del sistema de redirección para dispositivos móviles.', 'videoplayer'),
    ));

    // Enable redirects
    $wp_customize->add_setting('enable_redirects', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_redirects', array(
        'label' => __('Habilitar Redirecciones', 'videoplayer'),
        'description' => __('Activar el sistema de redirección automática.', 'videoplayer'),
        'section' => 'videoplayer_redirect_settings',
        'type' => 'checkbox',
    ));

    // Redirect URL
    $wp_customize->add_setting('redirect_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('redirect_url', array(
        'label' => __('URL de Redirección', 'videoplayer'),
        'description' => __('URL principal para las redirecciones.', 'videoplayer'),
        'section' => 'videoplayer_redirect_settings',
        'type' => 'url',
    ));

    // Max redirects per user
    $wp_customize->add_setting('max_redirects', array(
        'default' => 2,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('max_redirects', array(
        'label' => __('Máximo de Redirecciones', 'videoplayer'),
        'description' => __('Número máximo de redirecciones por usuario por día.', 'videoplayer'),
        'section' => 'videoplayer_redirect_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
        ),
    ));

    // Redirect delay
    $wp_customize->add_setting('redirect_delay', array(
        'default' => 3,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('redirect_delay', array(
        'label' => __('Retraso de Redirección (segundos)', 'videoplayer'),
        'description' => __('Tiempo de espera antes de la redirección.', 'videoplayer'),
        'section' => 'videoplayer_redirect_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 30,
        ),
    ));

    /**
     * Layout Settings Section
     */
    $wp_customize->add_section('videoplayer_layout', array(
        'title' => __('Diseño y Layout', 'videoplayer'),
        'priority' => 60,
        'description' => __('Opciones de diseño y estructura del sitio.', 'videoplayer'),
    ));

    // Layout style
    $wp_customize->add_setting('layout_style', array(
        'default' => 'grid',
        'sanitize_callback' => 'videoplayer_sanitize_layout_style',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('layout_style', array(
        'label' => __('Estilo de Layout', 'videoplayer'),
        'description' => __('Selecciona el estilo de visualización para los videos.', 'videoplayer'),
        'section' => 'videoplayer_layout',
        'type' => 'select',
        'choices' => array(
            'grid' => __('Cuadrícula', 'videoplayer'),
            'list' => __('Lista', 'videoplayer'),
            'masonry' => __('Mosaico', 'videoplayer'),
        ),
    ));

    // Sidebar position
    $wp_customize->add_setting('sidebar_position', array(
        'default' => 'right',
        'sanitize_callback' => 'videoplayer_sanitize_sidebar_position',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('sidebar_position', array(
        'label' => __('Posición del Sidebar', 'videoplayer'),
        'description' => __('Ubicación del sidebar en páginas internas.', 'videoplayer'),
        'section' => 'videoplayer_layout',
        'type' => 'select',
        'choices' => array(
            'left' => __('Izquierda', 'videoplayer'),
            'right' => __('Derecha', 'videoplayer'),
            'none' => __('Sin Sidebar', 'videoplayer'),
        ),
    ));

    // Container width
    $wp_customize->add_setting('container_width', array(
        'default' => 1200,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('container_width', array(
        'label' => __('Ancho del Contenedor (px)', 'videoplayer'),
        'description' => __('Ancho máximo del contenido principal.', 'videoplayer'),
        'section' => 'videoplayer_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 800,
            'max' => 1800,
            'step' => 50,
        ),
    ));

    /**
     * Header Settings Section
     */
    $wp_customize->add_section('videoplayer_header', array(
        'title' => __('Configuración del Header', 'videoplayer'),
        'priority' => 70,
        'description' => __('Personaliza la cabecera del sitio.', 'videoplayer'),
    ));

    // Header style
    $wp_customize->add_setting('header_style', array(
        'default' => 'modern',
        'sanitize_callback' => 'videoplayer_sanitize_header_style',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('header_style', array(
        'label' => __('Estilo del Header', 'videoplayer'),
        'section' => 'videoplayer_header',
        'type' => 'select',
        'choices' => array(
            'modern' => __('Moderno', 'videoplayer'),
            'classic' => __('Clásico', 'videoplayer'),
            'minimal' => __('Minimalista', 'videoplayer'),
        ),
    ));

    // Show search in header
    $wp_customize->add_setting('show_header_search', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('show_header_search', array(
        'label' => __('Mostrar Búsqueda en Header', 'videoplayer'),
        'section' => 'videoplayer_header',
        'type' => 'checkbox',
    ));

    // Sticky header
    $wp_customize->add_setting('sticky_header', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sticky_header', array(
        'label' => __('Header Pegajoso', 'videoplayer'),
        'description' => __('Mantener el header visible al hacer scroll.', 'videoplayer'),
        'section' => 'videoplayer_header',
        'type' => 'checkbox',
    ));

    /**
     * Footer Settings Section
     */
    $wp_customize->add_section('videoplayer_footer', array(
        'title' => __('Configuración del Footer', 'videoplayer'),
        'priority' => 80,
        'description' => __('Personaliza el pie de página del sitio.', 'videoplayer'),
    ));

    // Footer text
    $wp_customize->add_setting('footer_text', array(
        'default' => '© 2025 VideoPlayer Mobile. Todos los derechos reservados.',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('footer_text', array(
        'label' => __('Texto del Footer', 'videoplayer'),
        'section' => 'videoplayer_footer',
        'type' => 'textarea',
    ));

    // Show social links
    $wp_customize->add_setting('show_social_links', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_social_links', array(
        'label' => __('Mostrar Enlaces Sociales', 'videoplayer'),
        'section' => 'videoplayer_footer',
        'type' => 'checkbox',
    ));

    /**
     * Social Media Section
     */
    $wp_customize->add_section('videoplayer_social', array(
        'title' => __('Redes Sociales', 'videoplayer'),
        'priority' => 90,
        'description' => __('Enlaces a tus perfiles de redes sociales.', 'videoplayer'),
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'linkedin' => 'LinkedIn',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("social_{$network}", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("social_{$network}", array(
            'label' => $label . ' URL',
            'section' => 'videoplayer_social',
            'type' => 'url',
        ));
    }

    /**
     * Performance Section
     */
    $wp_customize->add_section('videoplayer_performance', array(
        'title' => __('Rendimiento', 'videoplayer'),
        'priority' => 100,
        'description' => __('Opciones para optimizar el rendimiento del sitio.', 'videoplayer'),
    ));

    // Enable caching
    $wp_customize->add_setting('enable_caching', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_caching', array(
        'label' => __('Habilitar Caché', 'videoplayer'),
        'description' => __('Activar sistema de caché para mejorar velocidad.', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox',
    ));

    // Optimize images
    $wp_customize->add_setting('optimize_images', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('optimize_images', array(
        'label' => __('Optimizar Imágenes', 'videoplayer'),
        'description' => __('Comprimir y optimizar imágenes automáticamente.', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox',
    ));

    // Minify CSS/JS
    $wp_customize->add_setting('minify_assets', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('minify_assets', array(
        'label' => __('Minificar CSS/JS', 'videoplayer'),
        'description' => __('Comprimir archivos CSS y JavaScript.', 'videoplayer'),
        'section' => 'videoplayer_performance',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'videoplayer_customize_register');

/**
 * Sanitization functions
 */
function videoplayer_sanitize_layout_style($input) {
    $valid = array('grid', 'list', 'masonry');
    return in_array($input, $valid) ? $input : 'grid';
}

function videoplayer_sanitize_sidebar_position($input) {
    $valid = array('left', 'right', 'none');
    return in_array($input, $valid) ? $input : 'right';
}

function videoplayer_sanitize_header_style($input) {
    $valid = array('modern', 'classic', 'minimal');
    return in_array($input, $valid) ? $input : 'modern';
}

/**
 * Bind JS handlers to instantly live-preview changes.
 */
function videoplayer_customize_preview_js() {
    wp_enqueue_script(
        'videoplayer-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array('customize-preview'),
        VIDEOPLAYER_VERSION,
        true
    );
}
add_action('customize_preview_init', 'videoplayer_customize_preview_js');

/**
 * Generate dynamic CSS based on customizer settings
 */
function videoplayer_customizer_css() {
    $primary_color = get_theme_mod('primary_color', '#ff6b6b');
    $secondary_color = get_theme_mod('secondary_color', '#4ecdc4');
    $background_color = get_theme_mod('background_color', '#0c0c0c');
    $text_color = get_theme_mod('text_color', '#ffffff');
    $container_width = get_theme_mod('container_width', 1200);
    
    $css = "
    :root {
        --primary-color: {$primary_color};
        --secondary-color: {$secondary_color};
        --background-color: {$background_color};
        --text-color: {$text_color};
        --container-width: {$container_width}px;
        --gradient-primary: linear-gradient(45deg, {$primary_color}, {$secondary_color});
    }
    
    body {
        background-color: {$background_color};
        color: {$text_color};
    }
    
    .container {
        max-width: {$container_width}px;
    }
    
    a {
        color: {$primary_color};
    }
    
    .btn-primary,
    .button-primary {
        background: var(--gradient-primary);
        border-color: {$primary_color};
    }
    
    .video-overlay .play-btn {
        background: {$primary_color};
    }
    
    .progress-fill {
        background: var(--gradient-primary);
    }
    ";
    
    // Header styles
    $header_style = get_theme_mod('header_style', 'modern');
    if ($header_style === 'minimal') {
        $css .= "
        .site-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(12, 12, 12, 0.95);
        }
        ";
    }
    
    // Sticky header
    if (get_theme_mod('sticky_header', true)) {
        $css .= "
        .site-header.sticky {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        body.has-sticky-header {
            padding-top: 80px;
        }
        ";
    }
    
    return $css;
}

/**
 * Output customizer CSS
 */
function videoplayer_customizer_css_output() {
    echo '<style type="text/css" id="videoplayer-customizer-css">';
    echo videoplayer_customizer_css();
    echo '</style>';
}
add_action('wp_head', 'videoplayer_customizer_css_output');

/**
 * Add customizer controls scripts
 */
function videoplayer_customizer_controls_scripts() {
    wp_enqueue_script(
        'videoplayer-customizer-controls',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array('jquery', 'customize-controls'),
        VIDEOPLAYER_VERSION,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'videoplayer_customizer_controls_scripts');

/**
 * Add custom CSS for customizer interface
 */
function videoplayer_customizer_controls_styles() {
    ?>
    <style>
    .customize-control-description {
        font-style: italic;
        color: #666;
        margin-top: 5px;
    }
    
    .customize-section-description {
        margin-bottom: 15px;
        padding: 10px;
        background: #f9f9f9;
        border-left: 4px solid #0073aa;
    }
    
    .videoplayer-customizer-heading {
        font-weight: 600;
        color: #0073aa;
        margin-bottom: 10px;
    }
    </style>
    <?php
}
add_action('customize_controls_print_styles', 'videoplayer_customizer_controls_styles');

/**
 * Register customizer panels (for complex customization)
 */
function videoplayer_customize_register_panels($wp_customize) {
    // Advanced Theme Panel
    $wp_customize->add_panel('videoplayer_advanced', array(
        'title' => __('Configuración Avanzada', 'videoplayer'),
        'description' => __('Opciones avanzadas de configuración del tema.', 'videoplayer'),
        'priority' => 200,
        'capability' => 'edit_theme_options',
    ));
}
add_action('customize_register', 'videoplayer_customize_register_panels', 5);