<?php
/**
 * Header template
 * 
 * @package VideoPlayerMobile
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <?php if (is_singular('video') && have_posts()) : the_post(); ?>
        <meta property="og:title" content="<?php the_title(); ?>">
        <meta property="og:description" content="<?php echo wp_strip_all_tags(get_the_excerpt()); ?>">
        <meta property="og:type" content="video.other">
        <meta property="og:url" content="<?php the_permalink(); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <meta property="og:image" content="<?php the_post_thumbnail_url('large'); ?>">
        <?php endif; ?>
        <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
        
        <meta name="twitter:card" content="player">
        <meta name="twitter:title" content="<?php the_title(); ?>">
        <meta name="twitter:description" content="<?php echo wp_strip_all_tags(get_the_excerpt()); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <meta name="twitter:image" content="<?php the_post_thumbnail_url('large'); ?>">
        <?php endif; ?>
        <?php rewind_posts(); ?>
    <?php endif; ?>
    
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-container">
    <header class="site-header">
        <div class="header-content">
            <div class="site-logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'videoplayer'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'nav-menu',
                    'container'      => false,
                    'fallback_cb'    => 'videoplayer_fallback_menu',
                ));
                ?>
            </nav>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-navigation">
                <span class="sr-only"><?php esc_html_e('Abrir menú', 'videoplayer'); ?></span>
                ☰
            </button>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <nav class="mobile-navigation" id="mobile-navigation" role="navigation" aria-label="<?php esc_attr_e('Mobile Menu', 'videoplayer'); ?>">
        <button class="mobile-nav-close" aria-label="<?php esc_attr_e('Cerrar menú', 'videoplayer'); ?>">×</button>
        
        <div class="mobile-nav-content">
            <!-- Search Form in Mobile Menu -->
            <div class="mobile-search">
                <?php get_search_form(); ?>
            </div>
            
            <?php
            wp_nav_menu(array(
                'theme_location' => 'mobile',
                'menu_class'     => 'mobile-nav-menu',
                'container'      => false,
                'fallback_cb'    => 'videoplayer_mobile_fallback_menu',
            ));
            ?>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay"></div>

    <main class="site-content" id="main">

<?php
/**
 * Fallback menu for primary navigation
 */
function videoplayer_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Inicio', 'videoplayer') . '</a></li>';
    
    // Videos page
    $videos_page = get_post_type_archive_link('video');
    if ($videos_page) {
        echo '<li><a href="' . esc_url($videos_page) . '">' . esc_html__('Videos', 'videoplayer') . '</a></li>';
    }
    
    // Categories
    $categories = get_categories(array('number' => 5));
    foreach ($categories as $category) {
        echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
    }
    
    echo '</ul>';
}

/**
 * Fallback menu for mobile navigation
 */
function videoplayer_mobile_fallback_menu() {
    echo '<ul class="mobile-nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Inicio', 'videoplayer') . '</a></li>';
    
    // Videos page
    $videos_page = get_post_type_archive_link('video');
    if ($videos_page) {
        echo '<li><a href="' . esc_url($videos_page) . '">' . esc_html__('Videos', 'videoplayer') . '</a></li>';
    }
    
    // Categories
    $categories = get_categories(array('number' => 5));
    foreach ($categories as $category) {
        echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
    }
    
    // Popular videos
    echo '<li><a href="' . esc_url(add_query_arg('orderby', 'popular', get_post_type_archive_link('video'))) . '">' . esc_html__('Populares', 'videoplayer') . '</a></li>';
    
    echo '</ul>';
}
?>