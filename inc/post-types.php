<?php
/**
 * Custom Post Types for VideoPlayer Mobile Theme
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Video Post Type
 */
function videoplayer_register_video_post_type() {
    $labels = array(
        'name'                  => _x('Videos', 'Post type general name', 'videoplayer'),
        'singular_name'         => _x('Video', 'Post type singular name', 'videoplayer'),
        'menu_name'             => _x('Videos', 'Admin Menu text', 'videoplayer'),
        'name_admin_bar'        => _x('Video', 'Add New on Toolbar', 'videoplayer'),
        'add_new'               => __('Agregar Nuevo', 'videoplayer'),
        'add_new_item'          => __('Agregar Nuevo Video', 'videoplayer'),
        'new_item'              => __('Nuevo Video', 'videoplayer'),
        'edit_item'             => __('Editar Video', 'videoplayer'),
        'view_item'             => __('Ver Video', 'videoplayer'),
        'all_items'             => __('Todos los Videos', 'videoplayer'),
        'search_items'          => __('Buscar Videos', 'videoplayer'),
        'parent_item_colon'     => __('Video Padre:', 'videoplayer'),
        'not_found'             => __('No se encontraron videos.', 'videoplayer'),
        'not_found_in_trash'    => __('No se encontraron videos en la papelera.', 'videoplayer'),
        'featured_image'        => _x('Imagen del Video', 'Overrides the "Featured Image" phrase', 'videoplayer'),
        'set_featured_image'    => _x('Establecer imagen del video', 'Overrides the "Set featured image" phrase', 'videoplayer'),
        'remove_featured_image' => _x('Eliminar imagen del video', 'Overrides the "Remove featured image" phrase', 'videoplayer'),
        'use_featured_image'    => _x('Usar como imagen del video', 'Overrides the "Use as featured image" phrase', 'videoplayer'),
        'archives'              => _x('Archivo de Videos', 'The post type archive label used in nav menus', 'videoplayer'),
        'insert_into_item'      => _x('Insertar en video', 'Overrides the "Insert into post" phrase', 'videoplayer'),
        'uploaded_to_this_item' => _x('Subido a este video', 'Overrides the "Uploaded to this post" phrase', 'videoplayer'),
        'filter_items_list'     => _x('Filtrar lista de videos', 'Screen reader text for the filter links', 'videoplayer'),
        'items_list_navigation' => _x('Navegación de lista de videos', 'Screen reader text for the pagination', 'videoplayer'),
        'items_list'            => _x('Lista de videos', 'Screen reader text for the items list', 'videoplayer'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'video'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-video-alt3',
        'supports'           => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
        'show_in_rest'       => true,
        'rest_base'          => 'videos',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'taxonomies'         => array('category', 'post_tag'),
    );

    register_post_type('video', $args);
}
add_action('init', 'videoplayer_register_video_post_type');

/**
 * Register Video Category Taxonomy
 */
function videoplayer_register_video_category_taxonomy() {
    $labels = array(
        'name'              => _x('Categorías de Video', 'taxonomy general name', 'videoplayer'),
        'singular_name'     => _x('Categoría de Video', 'taxonomy singular name', 'videoplayer'),
        'search_items'      => __('Buscar Categorías', 'videoplayer'),
        'all_items'         => __('Todas las Categorías', 'videoplayer'),
        'parent_item'       => __('Categoría Padre', 'videoplayer'),
        'parent_item_colon' => __('Categoría Padre:', 'videoplayer'),
        'edit_item'         => __('Editar Categoría', 'videoplayer'),
        'update_item'       => __('Actualizar Categoría', 'videoplayer'),
        'add_new_item'      => __('Agregar Nueva Categoría', 'videoplayer'),
        'new_item_name'     => __('Nombre de Nueva Categoría', 'videoplayer'),
        'menu_name'         => __('Categorías de Video', 'videoplayer'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'video-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('video_category', array('video'), $args);
}
add_action('init', 'videoplayer_register_video_category_taxonomy');

/**
 * Register Video Tag Taxonomy
 */
function videoplayer_register_video_tag_taxonomy() {
    $labels = array(
        'name'                       => _x('Etiquetas de Video', 'taxonomy general name', 'videoplayer'),
        'singular_name'              => _x('Etiqueta de Video', 'taxonomy singular name', 'videoplayer'),
        'search_items'               => __('Buscar Etiquetas', 'videoplayer'),
        'popular_items'              => __('Etiquetas Populares', 'videoplayer'),
        'all_items'                  => __('Todas las Etiquetas', 'videoplayer'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Editar Etiqueta', 'videoplayer'),
        'update_item'                => __('Actualizar Etiqueta', 'videoplayer'),
        'add_new_item'               => __('Agregar Nueva Etiqueta', 'videoplayer'),
        'new_item_name'              => __('Nombre de Nueva Etiqueta', 'videoplayer'),
        'separate_items_with_commas' => __('Separar etiquetas con comas', 'videoplayer'),
        'add_or_remove_items'        => __('Agregar o eliminar etiquetas', 'videoplayer'),
        'choose_from_most_used'      => __('Elegir de las más usadas', 'videoplayer'),
        'not_found'                  => __('No se encontraron etiquetas.', 'videoplayer'),
        'menu_name'                  => __('Etiquetas de Video', 'videoplayer'),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'video-tag'),
        'show_in_rest'          => true,
    );

    register_taxonomy('video_tag', array('video'), $args);
}
add_action('init', 'videoplayer_register_video_tag_taxonomy');

/**
 * Add custom columns to video admin list
 */
function videoplayer_add_video_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key === 'title') {
            $new_columns['video_thumbnail'] = __('Miniatura', 'videoplayer');
            $new_columns['video_duration'] = __('Duración', 'videoplayer');
            $new_columns['video_views'] = __('Visualizaciones', 'videoplayer');
            $new_columns['featured_video'] = __('Destacado', 'videoplayer');
        }
    }
    
    return $new_columns;
}
add_filter('manage_video_posts_columns', 'videoplayer_add_video_columns');

/**
 * Populate custom columns with data
 */
function videoplayer_populate_video_columns($column, $post_id) {
    switch ($column) {
        case 'video_thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(60, 40));
            } else {
                echo '<span style="color: #999;">Sin imagen</span>';
            }
            break;
            
        case 'video_duration':
            $duration = get_post_meta($post_id, '_video_duration', true);
            echo $duration ? esc_html($duration) : '<span style="color: #999;">No especificada</span>';
            break;
            
        case 'video_views':
            $views = get_post_meta($post_id, '_view_count', true);
            echo number_format($views ? intval($views) : 0);
            break;
            
        case 'featured_video':
            $featured = get_post_meta($post_id, '_featured_video', true);
            if ($featured == '1') {
                echo '<span style="color: #46b450;">⭐ Sí</span>';
            } else {
                echo '<span style="color: #999;">No</span>';
            }
            break;
    }
}
add_action('manage_video_posts_custom_column', 'videoplayer_populate_video_columns', 10, 2);

/**
 * Make custom columns sortable
 */
function videoplayer_video_sortable_columns($columns) {
    $columns['video_duration'] = 'video_duration';
    $columns['video_views'] = 'video_views';
    $columns['featured_video'] = 'featured_video';
    return $columns;
}
add_filter('manage_edit-video_sortable_columns', 'videoplayer_video_sortable_columns');

/**
 * Handle sorting by custom columns
 */
function videoplayer_video_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    switch ($orderby) {
        case 'video_duration':
            $query->set('meta_key', '_video_duration');
            $query->set('orderby', 'meta_value');
            break;
            
        case 'video_views':
            $query->set('meta_key', '_view_count');
            $query->set('orderby', 'meta_value_num');
            break;
            
        case 'featured_video':
            $query->set('meta_key', '_featured_video');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'videoplayer_video_column_orderby');

/**
 * Add meta boxes for video post type
 */
function videoplayer_add_video_meta_boxes() {
    add_meta_box(
        'video-details',
        __('Detalles del Video', 'videoplayer'),
        'videoplayer_video_details_meta_box',
        'video',
        'normal',
        'high'
    );
    
    add_meta_box(
        'video-seo',
        __('SEO del Video', 'videoplayer'),
        'videoplayer_video_seo_meta_box',
        'video',
        'side',
        'default'
    );
    
    add_meta_box(
        'video-analytics',
        __('Analíticas del Video', 'videoplayer'),
        'videoplayer_video_analytics_meta_box',
        'video',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'videoplayer_add_video_meta_boxes');

/**
 * Video details meta box callback
 */
function videoplayer_video_details_meta_box($post) {
    wp_nonce_field('videoplayer_save_video_meta', 'videoplayer_video_meta_nonce');
    
    $duration = get_post_meta($post->ID, '_video_duration', true);
    $video_url = get_post_meta($post->ID, '_video_url', true);
    $video_embed = get_post_meta($post->ID, '_video_embed', true);
    $redirect_enabled = get_post_meta($post->ID, '_redirect_enabled', true);
    $featured_video = get_post_meta($post->ID, '_featured_video', true);
    $video_quality = get_post_meta($post->ID, '_video_quality', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="video_duration"><?php _e('Duración del Video', 'videoplayer'); ?></label></th>
            <td>
                <input type="text" id="video_duration" name="video_duration" value="<?php echo esc_attr($duration); ?>" placeholder="5:30" />
                <p class="description"><?php _e('Formato: mm:ss (ejemplo: 5:30)', 'videoplayer'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th><label for="video_url"><?php _e('URL del Video', 'videoplayer'); ?></label></th>
            <td>
                <input type="url" id="video_url" name="video_url" value="<?php echo esc_attr($video_url); ?>" class="large-text" />
                <p class="description"><?php _e('URL directa del archivo de video (MP4, WebM, etc.)', 'videoplayer'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th><label for="video_embed"><?php _e('Código de Incrustación', 'videoplayer'); ?></label></th>
            <td>
                <textarea id="video_embed" name="video_embed" class="large-text" rows="5"><?php echo esc_textarea($video_embed); ?></textarea>
                <p class="description"><?php _e('Código iframe de YouTube, Vimeo, etc. (opcional)', 'videoplayer'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th><label for="video_quality"><?php _e('Calidad del Video', 'videoplayer'); ?></label></th>
            <td>
                <select id="video_quality" name="video_quality">
                    <option value="auto" <?php selected($video_quality, 'auto'); ?>><?php _e('Automática', 'videoplayer'); ?></option>
                    <option value="1080p" <?php selected($video_quality, '1080p'); ?>><?php _e('1080p (Full HD)', 'videoplayer'); ?></option>
                    <option value="720p" <?php selected($video_quality, '720p'); ?>><?php _e('720p (HD)', 'videoplayer'); ?></option>
                    <option value="480p" <?php selected($video_quality, '480p'); ?>><?php _e('480p (SD)', 'videoplayer'); ?></option>
                    <option value="360p" <?php selected($video_quality, '360p'); ?>><?php _e('360p', 'videoplayer'); ?></option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th><?php _e('Opciones del Video', 'videoplayer'); ?></th>
            <td>
                <p>
                    <label>
                        <input type="checkbox" id="redirect_enabled" name="redirect_enabled" value="1" <?php checked($redirect_enabled, '1'); ?> />
                        <?php _e('Habilitar sistema de redirección', 'videoplayer'); ?>
                    </label>
                </p>
                
                <p>
                    <label>
                        <input type="checkbox" id="featured_video" name="featured_video" value="1" <?php checked($featured_video, '1'); ?> />
                        <?php _e('Marcar como video destacado', 'videoplayer'); ?>
                    </label>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Video SEO meta box callback
 */
function videoplayer_video_seo_meta_box($post) {
    $seo_title = get_post_meta($post->ID, '_video_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_video_seo_description', true);
    $video_keywords = get_post_meta($post->ID, '_video_keywords', true);
    
    ?>
    <p>
        <label for="video_seo_title"><?php _e('Título SEO', 'videoplayer'); ?></label>
        <input type="text" id="video_seo_title" name="video_seo_title" value="<?php echo esc_attr($seo_title); ?>" class="widefat" />
    </p>
    
    <p>
        <label for="video_seo_description"><?php _e('Descripción SEO', 'videoplayer'); ?></label>
        <textarea id="video_seo_description" name="video_seo_description" class="widefat" rows="3"><?php echo esc_textarea($seo_description); ?></textarea>
    </p>
    
    <p>
        <label for="video_keywords"><?php _e('Palabras Clave', 'videoplayer'); ?></label>
        <input type="text" id="video_keywords" name="video_keywords" value="<?php echo esc_attr($video_keywords); ?>" class="widefat" />
        <small><?php _e('Separar con comas', 'videoplayer'); ?></small>
    </p>
    <?php
}

/**
 * Video analytics meta box callback
 */
function videoplayer_video_analytics_meta_box($post) {
    $views = get_post_meta($post->ID, '_view_count', true);
    $likes = get_post_meta($post->ID, '_like_count', true);
    $shares = get_post_meta($post->ID, '_share_count', true);
    
    ?>
    <div class="video-stats">
        <p><strong><?php _e('Visualizaciones:', 'videoplayer'); ?></strong> <?php echo number_format($views ? intval($views) : 0); ?></p>
        <p><strong><?php _e('Me gusta:', 'videoplayer'); ?></strong> <?php echo number_format($likes ? intval($likes) : 0); ?></p>
        <p><strong><?php _e('Compartidos:', 'videoplayer'); ?></strong> <?php echo number_format($shares ? intval($shares) : 0); ?></p>
        
        <hr>
        
        <p>
            <label for="manual_view_count"><?php _e('Ajustar visualizaciones:', 'videoplayer'); ?></label>
            <input type="number" id="manual_view_count" name="manual_view_count" value="<?php echo esc_attr($views); ?>" min="0" />
        </p>
    </div>
    <?php
}

/**
 * Save video meta data
 */
function videoplayer_save_video_meta($post_id) {
    // Check nonce
    if (!isset($_POST['videoplayer_video_meta_nonce']) || 
        !wp_verify_nonce($_POST['videoplayer_video_meta_nonce'], 'videoplayer_save_video_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save all meta fields
    $meta_fields = array(
        'video_duration',
        'video_url', 
        'video_embed',
        'video_quality',
        'redirect_enabled',
        'featured_video',
        'video_seo_title',
        'video_seo_description',
        'video_keywords',
        'manual_view_count'
    );
    
    foreach ($meta_fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'manual_view_count') {
                update_post_meta($post_id, '_view_count', intval($_POST[$field]));
            } else {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        } else {
            // For checkboxes, delete meta if not checked
            if (in_array($field, array('redirect_enabled', 'featured_video'))) {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }
}
add_action('save_post', 'videoplayer_save_video_meta');

/**
 * Flush rewrite rules on activation
 */
function videoplayer_flush_rewrite_rules() {
    videoplayer_register_video_post_type();
    videoplayer_register_video_category_taxonomy();
    videoplayer_register_video_tag_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'videoplayer_flush_rewrite_rules');

/**
 * Add custom meta fields to REST API
 */
function videoplayer_register_video_meta_rest() {
    register_rest_field('video', 'video_meta', array(
        'get_callback' => function($post) {
            return array(
                'duration' => get_post_meta($post['id'], '_video_duration', true),
                'views' => intval(get_post_meta($post['id'], '_view_count', true)),
                'featured' => get_post_meta($post['id'], '_featured_video', true) == '1',
                'redirect_enabled' => get_post_meta($post['id'], '_redirect_enabled', true) == '1',
                'video_url' => get_post_meta($post['id'], '_video_url', true),
                'quality' => get_post_meta($post['id'], '_video_quality', true),
            );
        },
        'update_callback' => null,
        'schema' => null,
    ));
}
add_action('rest_api_init', 'videoplayer_register_video_meta_rest');

/**
 * Add video post type to main query
 */
function videoplayer_include_videos_in_home($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_home()) {
            $query->set('post_type', array('post', 'video'));
        }
    }
}
add_action('pre_get_posts', 'videoplayer_include_videos_in_home');

/**
 * Custom video post type messages
 */
function videoplayer_video_updated_messages($messages) {
    $post = get_post();
    $post_type = get_post_type($post);
    $post_type_object = get_post_type_object($post_type);

    $messages['video'] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => __('Video actualizado.', 'videoplayer'),
        2  => __('Campo personalizado actualizado.', 'videoplayer'),
        3  => __('Campo personalizado eliminado.', 'videoplayer'),
        4  => __('Video actualizado.', 'videoplayer'),
        5  => isset($_GET['revision']) ? sprintf(__('Video restaurado a revisión del %s', 'videoplayer'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6  => __('Video publicado.', 'videoplayer'),
        7  => __('Video guardado.', 'videoplayer'),
        8  => __('Video enviado.', 'videoplayer'),
        9  => sprintf(
            __('Video programado para: <strong>%1$s</strong>.', 'videoplayer'),
            date_i18n(__('M j, Y @ G:i', 'videoplayer'), strtotime($post->post_date))
        ),
        10 => __('Borrador de video actualizado.', 'videoplayer')
    );

    if ($post_type_object->publicly_queryable && 'video' === $post_type) {
        $permalink = get_permalink($post->ID);

        $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('Ver video', 'videoplayer'));
        $messages['video'][1] .= $view_link;
        $messages['video'][6] .= $view_link;
        $messages['video'][9] .= $view_link;

        $preview_permalink = add_query_arg('preview', 'true', $permalink);
        $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Vista previa del video', 'videoplayer'));
        $messages['video'][8]  .= $preview_link;
        $messages['video'][10] .= $preview_link;
    }

    return $messages;
}
add_filter('post_updated_messages', 'videoplayer_video_updated_messages');