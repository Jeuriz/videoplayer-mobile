<?php
/**
 * Template part for displaying video cards
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('video-card'); ?>>
    <div class="video-thumbnail">
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
            
            <?php if (get_theme_mod('show_video_duration', true)) : ?>
                <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
            <?php endif; ?>
            
            <?php if (is_featured_video()) : ?>
                <div class="video-badges">
                    <span class="featured-badge"><?php esc_html_e('Destacado', 'videoplayer'); ?></span>
                </div>
            <?php endif; ?>
        </a>
    </div>
    
    <div class="video-content">
        <h3 class="video-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <div class="video-meta">
            <?php if (get_theme_mod('show_view_count', true)) : ?>
                <span class="views">👁️ <?php echo number_format(get_video_views()); ?></span>
            <?php endif; ?>
            <span class="date">📅 <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
            <span class="author">👤 <?php the_author(); ?></span>
        </div>
        
        <div class="video-excerpt">
            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
        </div>
        
        <?php if (has_category()) : ?>
            <div class="video-categories">
                <?php the_category(' '); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
