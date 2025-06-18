<?php
/**
 * Video Functions for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get video duration in formatted string
 */
function get_video_duration($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $duration = get_post_meta($post_id, '_video_duration', true);
    return $duration ? $duration : '0:00';
}

/**
 * Get video views count
 */
function get_video_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $views = get_post_meta($post_id, '_view_count', true);
    return $views ? intval($views) : 0;
}

/**
 * Update video views count
 */
function update_video_views($post_id) {
    $views = get_video_views($post_id);
    update_post_meta($post_id, '_view_count', $views + 1);
    return $views + 1;
}

/**
 * Check if video has redirect enabled
 */
function is_video_redirect_enabled($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_redirect_enabled', true) == '1';
}

/**
 * Alias function for backward compatibility
 */
function is_redirect_enabled($post_id = null) {
    return is_video_redirect_enabled($post_id);
}

/**
 * Check if video is featured
 */
function is_featured_video($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_featured_video', true) == '1';
}

/**
 * Get video URL
 */
function get_video_url($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_video_url', true);
}

/**
 * Get video embed code
 */
function get_video_embed($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_video_embed', true);
}

/**
 * Get video quality
 */
function get_video_quality($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $quality = get_post_meta($post_id, '_video_quality', true);
    return $quality ? $quality : 'auto';
}

/**
 * Format video duration from seconds to mm:ss
 */
function format_video_duration($seconds) {
    if (!is_numeric($seconds)) {
        return $seconds; // Return as-is if already formatted
    }
    
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    
    return sprintf('%d:%02d', $minutes, $seconds);
}

/**
 * Parse video duration from mm:ss to seconds
 */
function parse_video_duration($duration) {
    if (is_numeric($duration)) {
        return intval($duration); // Already in seconds
    }
    
    $parts = explode(':', $duration);
    if (count($parts) == 2) {
        return (intval($parts[0]) * 60) + intval($parts[1]);
    }
    
    return 0;
}

/**
 * Get featured videos
 */
function get_featured_videos($limit = 5) {
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => $limit,
        'meta_query' => array(
            array(
                'key' => '_featured_video',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return new WP_Query($args);
}

/**
 * Get popular videos based on views
 */
function get_popular_videos($limit = 10, $days = 30) {
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => $limit,
        'meta_key' => '_view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => $days . ' days ago'
            )
        )
    );
    
    return new WP_Query($args);
}

/**
 * Get related videos based on categories
 */
function get_related_videos($post_id = null, $limit = 6) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_the_terms($post_id, 'category');
    $category_ids = array();
    
    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }
    }
    
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'orderby' => 'rand'
    );
    
    if (!empty($category_ids)) {
        $args['category__in'] = $category_ids;
    }
    
    return new WP_Query($args);
}

/**
 * Get video thumbnail with fallback
 */
function get_video_thumbnail($post_id = null, $size = 'medium') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, $size);
    }
    
    // Fallback placeholder
    $placeholder_url = get_template_directory_uri() . '/assets/images/video-placeholder.jpg';
    return '<img src="' . esc_url($placeholder_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '" class="video-thumbnail-placeholder">';
}

/**
 * Generate video schema markup for SEO
 */
function get_video_schema_markup($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $duration = get_video_duration($post_id);
    $views = get_video_views($post_id);
    $video_url = get_video_url($post_id);
    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'VideoObject',
        'name' => get_the_title($post_id),
        'description' => get_the_excerpt($post_id),
        'uploadDate' => get_the_date('c', $post_id),
        'duration' => 'PT' . parse_video_duration($duration) . 'S',
        'interactionCount' => $views,
        'url' => get_permalink($post_id)
    );
    
    if ($thumbnail_url) {
        $schema['thumbnailUrl'] = $thumbnail_url;
    }
    
    if ($video_url) {
        $schema['contentUrl'] = $video_url;
    }
    
    $author = get_userdata(get_post_field('post_author', $post_id));
    if ($author) {
        $schema['author'] = array(
            '@type' => 'Person',
            'name' => $author->display_name
        );
    }
    
    return '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}

/**
 * Track video interaction (like, share, etc.)
 */
function track_video_interaction($post_id, $type, $increment = 1) {
    $meta_key = '_' . $type . '_count';
    $current_count = get_post_meta($post_id, $meta_key, true);
    $new_count = intval($current_count) + $increment;
    
    update_post_meta($post_id, $meta_key, $new_count);
    
    return $new_count;
}

/**
 * Get video interaction count
 */
function get_video_interaction_count($post_id, $type) {
    $meta_key = '_' . $type . '_count';
    $count = get_post_meta($post_id, $meta_key, true);
    
    return $count ? intval($count) : 0;
}

/**
 * Check if user can view video (for premium content)
 */
function can_user_view_video($post_id = null, $user_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    // Check if video is premium
    $is_premium = get_post_meta($post_id, '_premium_video', true);
    
    if ($is_premium != '1') {
        return true; // Free video, everyone can view
    }
    
    // Check user capabilities for premium videos
    if (!$user_id) {
        return false; // Not logged in
    }
    
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }
    
    // Check if user has premium access
    $has_premium = get_user_meta($user_id, '_has_premium_access', true);
    
    return $has_premium == '1' || user_can($user_id, 'manage_options');
}

/**
 * Get video categories for filtering
 */
function get_video_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => true,
        'meta_query' => array(
            array(
                'key' => '_video_category',
                'compare' => 'EXISTS'
            )
        )
    ));
    
    return $categories;
}

/**
 * Get video tags cloud
 */
function get_video_tags_cloud($args = array()) {
    $defaults = array(
        'smallest' => 12,
        'largest' => 24,
        'unit' => 'px',
        'number' => 45,
        'taxonomy' => array('post_tag', 'video_tag'),
        'post_type' => 'video'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    return wp_tag_cloud($args);
}

/**
 * Generate video playlist
 */
function generate_video_playlist($videos, $current_video_id = null) {
    if (!$videos || !$videos->have_posts()) {
        return '';
    }
    
    $playlist = '<div class="video-playlist">';
    $playlist .= '<h3>' . __('Lista de Reproducción', 'videoplayer') . '</h3>';
    $playlist .= '<ul class="playlist-items">';
    
    $index = 1;
    while ($videos->have_posts()) {
        $videos->the_post();
        $is_current = (get_the_ID() == $current_video_id);
        $class = $is_current ? 'playlist-item current' : 'playlist-item';
        
        $playlist .= '<li class="' . $class . '" data-video-id="' . get_the_ID() . '">';
        $playlist .= '<div class="playlist-thumbnail">';
        
        if (has_post_thumbnail()) {
            $playlist .= get_the_post_thumbnail(get_the_ID(), 'thumbnail');
        } else {
            $playlist .= '<div class="thumbnail-placeholder">📹</div>';
        }
        
        $playlist .= '<div class="play-overlay">▶</div>';
        $playlist .= '</div>';
        
        $playlist .= '<div class="playlist-info">';
        $playlist .= '<div class="playlist-index">' . $index . '</div>';
        $playlist .= '<div class="playlist-title">' . get_the_title() . '</div>';
        $playlist .= '<div class="playlist-duration">' . get_video_duration() . '</div>';
        $playlist .= '</div>';
        
        $playlist .= '</li>';
        $index++;
    }
    
    wp_reset_postdata();
    
    $playlist .= '</ul>';
    $playlist .= '</div>';
    
    return $playlist;
}

/**
 * Get video quality options
 */
function get_video_quality_options() {
    return array(
        'auto' => __('Automática', 'videoplayer'),
        '1080p' => __('1080p (Full HD)', 'videoplayer'),
        '720p' => __('720p (HD)', 'videoplayer'),
        '480p' => __('480p (SD)', 'videoplayer'),
        '360p' => __('360p', 'videoplayer'),
        '240p' => __('240p', 'videoplayer')
    );
}

/**
 * Extract video ID from YouTube URL
 */
function extract_youtube_id($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : false;
}

/**
 * Extract video ID from Vimeo URL
 */
function extract_vimeo_id($url) {
    preg_match('/(?:vimeo\.com\/)(\d+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : false;
}

/**
 * Get video provider from URL
 */
function get_video_provider($url) {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        return 'youtube';
    } elseif (strpos($url, 'vimeo.com') !== false) {
        return 'vimeo';
    } elseif (strpos($url, 'dailymotion.com') !== false) {
        return 'dailymotion';
    }
    
    return 'direct';
}

/**
 * Generate responsive video embed
 */
function generate_responsive_video_embed($url, $width = 560, $height = 315) {
    $provider = get_video_provider($url);
    $embed_code = '';
    
    switch ($provider) {
        case 'youtube':
            $video_id = extract_youtube_id($url);
            if ($video_id) {
                $embed_code = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $video_id . '?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>';
            }
            break;
            
        case 'vimeo':
            $video_id = extract_vimeo_id($url);
            if ($video_id) {
                $embed_code = '<iframe width="' . $width . '" height="' . $height . '" src="https://player.vimeo.com/video/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
            }
            break;
            
        case 'direct':
            $embed_code = '<video width="' . $width . '" height="' . $height . '" controls><source src="' . esc_url($url) . '" type="video/mp4"></video>';
            break;
    }
    
    if ($embed_code) {
        return '<div class="responsive-video-wrapper">' . $embed_code . '</div>';
    }
    
    return '';
}

/**
 * Calculate estimated reading time for video description
 */
function videoplayer_estimated_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    
    return max(1, $reading_time);
}

/**
 * Get video statistics summary
 */
function get_video_stats_summary($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return array(
        'views' => get_video_views($post_id),
        'likes' => get_video_interaction_count($post_id, 'like'),
        'shares' => get_video_interaction_count($post_id, 'share'),
        'comments' => get_comments_number($post_id),
        'duration' => get_video_duration($post_id),
        'quality' => get_video_quality($post_id),
        'featured' => is_featured_video($post_id),
        'redirect_enabled' => is_video_redirect_enabled($post_id)
    );
}

/**
 * AJAX handler for video interactions
 */
function videoplayer_ajax_video_interaction() {
    check_ajax_referer('videoplayer_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id']);
    $action_type = sanitize_text_field($_POST['action_type']);
    
    if (!$post_id || !in_array($action_type, array('like', 'share', 'save'))) {
        wp_send_json_error('Invalid parameters');
        return;
    }
    
    $new_count = track_video_interaction($post_id, $action_type);
    
    wp_send_json_success(array(
        'count' => $new_count,
        'formatted_count' => number_format($new_count)
    ));
}
add_action('wp_ajax_video_interaction', 'videoplayer_ajax_video_interaction');
add_action('wp_ajax_nopriv_video_interaction', 'videoplayer_ajax_video_interaction');

/**
 * AJAX handler for tracking video views
 */
function videoplayer_ajax_track_view() {
    check_ajax_referer('videoplayer_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id']);
    
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
        return;
    }
    
    // Check if already viewed by this IP today
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $view_key = 'video_view_' . $post_id . '_' . md5($user_ip . date('Y-m-d'));
    
    if (!get_transient($view_key)) {
        $new_count = update_video_views($post_id);
        set_transient($view_key, true, DAY_IN_SECONDS);
        
        wp_send_json_success(array(
            'views' => $new_count,
            'formatted_views' => number_format($new_count)
        ));
    } else {
        wp_send_json_success(array(
            'views' => get_video_views($post_id),
            'already_counted' => true
        ));
    }
}
add_action('wp_ajax_track_video_view', 'videoplayer_ajax_track_view');
add_action('wp_ajax_nopriv_track_video_view', 'videoplayer_ajax_track_view');

/**
 * Add video meta to head for social sharing
 */
function videoplayer_add_video_meta_head() {
    if (is_singular('video')) {
        $post_id = get_the_ID();
        $video_url = get_video_url($post_id);
        $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
        $duration = get_video_duration($post_id);
        
        echo '<meta property="og:type" content="video.other">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_the_excerpt()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        
        if ($thumbnail_url) {
            echo '<meta property="og:image" content="' . esc_url($thumbnail_url) . '">' . "\n";
        }
        
        if ($video_url) {
            echo '<meta property="og:video" content="' . esc_url($video_url) . '">' . "\n";
            echo '<meta property="og:video:type" content="video/mp4">' . "\n";
        }
        
        echo '<meta name="twitter:card" content="player">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr(get_the_excerpt()) . '">' . "\n";
        
        if ($thumbnail_url) {
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail_url) . '">' . "\n";
        }
        
        // Video schema markup
        echo get_video_schema_markup($post_id);
    }
}
add_action('wp_head', 'videoplayer_add_video_meta_head');

/**
 * Custom video search functionality
 */
function videoplayer_video_search($search_term, $limit = 10) {
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => $limit,
        's' => $search_term,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_video_keywords',
                'value' => $search_term,
                'compare' => 'LIKE'
            )
        )
    );
    
    return new WP_Query($args);
}