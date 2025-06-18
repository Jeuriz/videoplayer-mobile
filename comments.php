<?php
/**
 * Comments Template
 * 
 * @package VideoPlayerMobile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<section id="comments" class="comments-area">
    
    <?php if (have_comments()) : ?>
        
        <div class="comments-header">
            <h3 class="comments-title">
                <?php
                $comment_count = get_comments_number();
                if ($comment_count === 1) {
                    printf(
                        esc_html__('Un comentario en &ldquo;%1$s&rdquo;', 'videoplayer'),
                        '<span>' . wp_kses_post(get_the_title()) . '</span>'
                    );
                } else {
                    printf(
                        esc_html(_nx(
                            '%1$s comentario en &ldquo;%2$s&rdquo;',
                            '%1$s comentarios en &ldquo;%2$s&rdquo;',
                            $comment_count,
                            'comments title',
                            'videoplayer'
                        )),
                        number_format_i18n($comment_count),
                        '<span>' . wp_kses_post(get_the_title()) . '</span>'
                    );
                }
                ?>
            </h3>
            
            <div class="comments-stats">
                <span class="comments-count">💬 <?php echo get_comments_number(); ?> comentarios</span>
                <?php if (get_option('thread_comments')) : ?>
                    <span class="comments-threading">🧵 Conversación anidada</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments Sort Options -->
        <div class="comments-controls">
            <div class="comments-sort">
                <label for="comment-sort"><?php esc_html_e('Ordenar por:', 'videoplayer'); ?></label>
                <select id="comment-sort" onchange="sortComments(this.value)">
                    <option value="newest"><?php esc_html_e('Más recientes', 'videoplayer'); ?></option>
                    <option value="oldest"><?php esc_html_e('Más antiguos', 'videoplayer'); ?></option>
                    <option value="popular"><?php esc_html_e('Más populares', 'videoplayer'); ?></option>
                </select>
            </div>
            
            <?php if (get_option('thread_comments')) : ?>
                <div class="comments-view-toggle">
                    <button class="view-toggle active" data-view="threaded" onclick="toggleCommentsView('threaded')">
                        <?php esc_html_e('Vista anidada', 'videoplayer'); ?>
                    </button>
                    <button class="view-toggle" data-view="flat" onclick="toggleCommentsView('flat')">
                        <?php esc_html_e('Vista plana', 'videoplayer'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Comments List -->
        <ol class="comments-list" id="comments-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'videoplayer_comment_callback',
            ));
            ?>
        </ol>

        <!-- Comments Pagination -->
        <?php
        $prev_link = get_previous_comments_link(__('← Comentarios anteriores', 'videoplayer'));
        $next_link = get_next_comments_link(__('Comentarios siguientes →', 'videoplayer'));
        
        if ($prev_link || $next_link) :
        ?>
            <nav class="comments-pagination" role="navigation" aria-label="<?php esc_attr_e('Navegación de comentarios', 'videoplayer'); ?>">
                <div class="nav-links">
                    <?php if ($prev_link) : ?>
                        <div class="nav-previous"><?php echo $prev_link; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($next_link) : ?>
                        <div class="nav-next"><?php echo $next_link; ?></div>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; // Check for have_comments(). ?>

    <!-- Comment Form Section -->
    <?php if (comments_open()) : ?>
        
        <div class="comment-form-section">
            <div class="comment-form-header">
                <h3 class="comment-form-title">
                    <?php esc_html_e('Deja tu comentario', 'videoplayer'); ?>
                </h3>
                <p class="comment-form-description">
                    <?php esc_html_e('Comparte tu opinión sobre este contenido. Mantén el respeto y la cortesía.', 'videoplayer'); ?>
                </p>
            </div>

            <?php
            $commenter = wp_get_current_commenter();
            $req = get_option('require_name_email');
            $aria_req = ($req ? ' aria-required="true" required' : '');
            
            $comment_form_args = array(
                'title_reply'         => '',
                'title_reply_to'      => esc_html__('Responder a %s', 'videoplayer'),
                'title_reply_before'  => '<h3 id="reply-title" class="comment-reply-title">',
                'title_reply_after'   => '</h3>',
                'cancel_reply_before' => '<small>',
                'cancel_reply_after'  => '</small>',
                'cancel_reply_link'   => esc_html__('Cancelar respuesta', 'videoplayer'),
                'label_submit'        => esc_html__('Publicar comentario', 'videoplayer'),
                'submit_button'       => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s">',
                'submit_field'        => '<div class="form-submit">%1$s %2$s</div>',
                'comment_field'       => '<div class="comment-form-comment"><label for="comment" class="comment-form-label">' . esc_html__('Comentario', 'videoplayer') . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" placeholder="' . esc_attr__('Escribe tu comentario aquí...', 'videoplayer') . '"' . $aria_req . '></textarea><div class="comment-guidelines"><small>' . esc_html__('Los comentarios están moderados. Mantén el respeto y evita contenido inapropiado.', 'videoplayer') . '</small></div></div>',
                'fields'              => array(
                    'author' => '<div class="comment-form-author"><label for="author" class="comment-form-label">' . esc_html__('Nombre', 'videoplayer') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" placeholder="' . esc_attr__('Tu nombre', 'videoplayer') . '"' . $aria_req . ' /></div>',
                    'email'  => '<div class="comment-form-email"><label for="email" class="comment-form-label">' . esc_html__('Email', 'videoplayer') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" placeholder="' . esc_attr__('tu@email.com', 'videoplayer') . '"' . $aria_req . ' /><small id="email-notes">' . esc_html__('No será publicado', 'videoplayer') . '</small></div>',
                    'url'    => '<div class="comment-form-url"><label for="url" class="comment-form-label">' . esc_html__('Sitio web', 'videoplayer') . '</label><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" placeholder="' . esc_attr__('https://tusitio.com (opcional)', 'videoplayer') . '" /></div>',
                ),
                'class_form'          => 'comment-form',
                'class_submit'        => 'submit btn btn-primary',
                'comment_notes_before' => '<div class="comment-notes"><p class="comment-notes-text">' . esc_html__('Tu dirección de email no será publicada.', 'videoplayer') . ($req ? ' ' . esc_html__('Los campos obligatorios están marcados con *', 'videoplayer') : '') . '</p></div>',
                'comment_notes_after'  => '',
            );

            comment_form($comment_form_args);
            ?>
        </div>

    <?php elseif (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        
        <div class="comments-closed">
            <div class="comments-closed-icon">🔒</div>
            <h3><?php esc_html_e('Los comentarios están cerrados', 'videoplayer'); ?></h3>
            <p><?php esc_html_e('Ya no se pueden agregar nuevos comentarios a este contenido.', 'videoplayer'); ?></p>
        </div>

    <?php endif; ?>

</section><!-- #comments -->

<?php
/**
 * Custom comment callback function
 */
function videoplayer_comment_callback($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
        
        <?php if ('div' != $args['style']) : ?>
            <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
        <?php endif; ?>
        
        <div class="comment-meta">
            <div class="comment-author vcard">
                <?php
                if ($args['avatar_size'] != 0) {
                    echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'comment-avatar'));
                }
                ?>
                
                <div class="comment-author-info">
                    <div class="comment-author-name">
                        <?php
                        $author_name = get_comment_author_link();
                        if (!empty($author_name)) {
                            echo $author_name;
                        }
                        ?>
                        
                        <?php if (get_comment_meta(get_comment_ID(), 'verified_user', true)) : ?>
                            <span class="verified-badge" title="<?php esc_attr_e('Usuario verificado', 'videoplayer'); ?>">✓</span>
                        <?php endif; ?>
                        
                        <?php if (user_can(get_comment_author_email(), 'administrator')) : ?>
                            <span class="admin-badge" title="<?php esc_attr_e('Administrador', 'videoplayer'); ?>">👑</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="comment-metadata">
                        <time datetime="<?php comment_time('c'); ?>" title="<?php echo get_comment_date() . ' ' . get_comment_time(); ?>">
                            <?php echo human_time_diff(get_comment_time('U'), current_time('timestamp')) . ' ago'; ?>
                        </time>
                        
                        <?php edit_comment_link(esc_html__('Editar', 'videoplayer'), '<span class="comment-edit-link">', '</span>'); ?>
                        
                        <?php if (get_option('thread_comments') && $depth < $args['max_depth']) : ?>
                            <span class="comment-reply">
                                <?php
                                comment_reply_link(array_merge($args, array(
                                    'add_below' => $add_below,
                                    'depth'     => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'reply_text' => esc_html__('Responder', 'videoplayer'),
                                )));
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="comment-content">
            <?php if ($comment->comment_approved == '0') : ?>
                <em class="comment-awaiting-moderation">
                    <?php esc_html_e('Tu comentario está esperando moderación.', 'videoplayer'); ?>
                </em>
            <?php endif; ?>
            
            <div class="comment-text">
                <?php comment_text(); ?>
            </div>
            
            <!-- Comment Actions -->
            <div class="comment-actions">
                <div class="comment-voting">
                    <button class="vote-btn upvote" data-comment-id="<?php comment_ID(); ?>" data-vote="up">
                        <span class="vote-icon">👍</span>
                        <span class="vote-count"><?php echo get_comment_meta(get_comment_ID(), 'upvotes', true) ?: 0; ?></span>
                    </button>
                    
                    <button class="vote-btn downvote" data-comment-id="<?php comment_ID(); ?>" data-vote="down">
                        <span class="vote-icon">👎</span>
                        <span class="vote-count"><?php echo get_comment_meta(get_comment_ID(), 'downvotes', true) ?: 0; ?></span>
                    </button>
                </div>
                
                <div class="comment-tools">
                    <button class="comment-share" data-comment-id="<?php comment_ID(); ?>">
                        <span class="icon">📤</span>
                        <?php esc_html_e('Compartir', 'videoplayer'); ?>
                    </button>
                    
                    <button class="comment-report" data-comment-id="<?php comment_ID(); ?>">
                        <span class="icon">🚩</span>
                        <?php esc_html_e('Reportar', 'videoplayer'); ?>
                    </button>
                </div>
            </div>
        </div>

        <?php if ('div' != $args['style']) : ?>
            </div>
        <?php endif; ?>
    
    <?php // Note: </li> is added automatically by WordPress ?>
    <?php
}
?>

<style>
.comments-area {
    margin: 40px 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    padding: 30px;
}

.comments-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.comments-title {
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.comments-stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    font-size: 14px;
    color: var(--muted-text);
}

.comments-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.comments-sort {
    display: flex;
    align-items: center;
    gap: 10px;
}

.comments-sort label {
    font-size: 14px;
    color: var(--muted-text);
}

.comments-sort select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 6px 12px;
    border-radius: 4px;
}

.comments-view-toggle {
    display: flex;
    gap: 5px;
}

.view-toggle {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 12px;
}

.view-toggle:hover,
.view-toggle.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.comments-list {
    list-style: none;
    padding: 0;
    margin: 0 0 30px 0;
}

.comment {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    padding: 20px;
    transition: var(--transition);
}

.comment:hover {
    background: rgba(255, 255, 255, 0.08);
}

.comment.parent {
    background: rgba(255, 255, 255, 0.08);
}

.children {
    list-style: none;
    padding: 0;
    margin: 20px 0 0 40px;
}

.children .comment {
    background: rgba(255, 255, 255, 0.03);
    border-left: 3px solid var(--primary-color);
}

.comment-body {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.comment-meta {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.comment-avatar {
    border-radius: 50%;
    border: 2px solid var(--border-color);
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.comment-author-info {
    flex: 1;
}

.comment-author-name {
    font-weight: 600;
    color: var(--light-text);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.comment-author-name a {
    color: var(--primary-color);
    text-decoration: none;
}

.verified-badge {
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 600;
}

.admin-badge {
    background: gold;
    color: #333;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.comment-metadata {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: var(--muted-text);
}

.comment-metadata a {
    color: var(--muted-text);
    text-decoration: none;
    transition: var(--transition);
}

.comment-metadata a:hover {
    color: var(--primary-color);
}

.comment-content {
    margin-left: 65px;
}

.comment-awaiting-moderation {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid rgba(255, 193, 7, 0.3);
    margin-bottom: 15px;
    display: block;
}

.comment-text {
    line-height: 1.6;
    color: var(--light-text);
    margin-bottom: 15px;
}

.comment-text p {
    margin-bottom: 10px;
}

.comment-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.comment-voting {
    display: flex;
    gap: 10px;
}

.vote-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 6px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
}

.vote-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.vote-btn.upvote.voted {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border-color: #4caf50;
}

.vote-btn.downvote.voted {
    background: rgba(244, 67, 54, 0.2);
    color: #f44336;
    border-color: #f44336;
}

.comment-tools {
    display: flex;
    gap: 10px;
}

.comment-share,
.comment-report {
    background: none;
    border: none;
    color: var(--muted-text);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 4px;
}

.comment-share:hover {
    color: var(--primary-color);
    background: rgba(255, 255, 255, 0.1);
}

.comment-report:hover {
    color: #f44336;
    background: rgba(255, 255, 255, 0.1);
}

.comments-pagination {
    text-align: center;
    margin: 30px 0;
}

.comments-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.comments-pagination a {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light-text);
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.comments-pagination a:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.comment-form-section {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid var(--border-color);
}

.comment-form-header {
    text-align: center;
    margin-bottom: 30px;
}

.comment-form-title {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.comment-form-description {
    color: var(--muted-text);
    font-size: 14px;
}

.comment-form {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 25px;
}

.comment-notes {
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 6px;
    color: #ffc107;
    font-size: 14px;
}

.comment-form-author,
.comment-form-email,
.comment-form-url,
.comment-form-comment {
    margin-bottom: 20px;
}

.comment-form-label {
    display: block;
    color: var(--light-text);
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.required {
    color: var(--primary-color);
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form input[type="url"],
.comment-form textarea {
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--light-text);
    padding: 12px 15px;
    border-radius: 6px;
    font-size: 14px;
    transition: var(--transition);
    outline: none;
}

.comment-form input[type="text"]:focus,
.comment-form input[type="email"]:focus,
.comment-form input[type="url"]:focus,
.comment-form textarea:focus {
    border-color: var(--primary-color);
    background: rgba(255, 255, 255, 0.15);
}

.comment-form textarea {
    resize: vertical;
    min-height: 120px;
}

.comment-form input::placeholder,
.comment-form textarea::placeholder {
    color: var(--muted-text);
}

.comment-guidelines {
    margin-top: 8px;
}

.comment-guidelines small {
    color: var(--muted-text);
    font-size: 12px;
    line-height: 1.4;
}

.form-submit {
    margin-top: 25px;
}

.comment-form .btn {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
}

.comment-form .btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-light);
}

.comments-closed {
    text-align: center;
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
}

.comments-closed-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.6;
}

.comments-closed h3 {
    color: var(--muted-text);
    margin-bottom: 10px;
}

.comments-closed p {
    color: var(--muted-text);
    font-size: 14px;
}

@media (max-width: 768px) {
    .comments-area {
        padding: 20px;
    }
    
    .comments-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .comment-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .comment-content {
        margin-left: 0;
    }
    
    .children {
        margin-left: 20px;
    }
    
    .comment-actions {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .comment-voting,
    .comment-tools {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .comments-stats {
        flex-direction: column;
        gap: 5px;
    }
    
    .comment-avatar {
        width: 40px;
        height: 40px;
    }
    
    .children {
        margin-left: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initCommentsFunctionality();
});

function initCommentsFunctionality() {
    // Comment voting
    const voteButtons = document.querySelectorAll('.vote-btn');
    voteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const voteType = this.dataset.vote;
            handleCommentVote(commentId, voteType, this);
        });
    });
    
    // Comment sharing
    const shareButtons = document.querySelectorAll('.comment-share');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            shareComment(commentId);
        });
    });
    
    // Comment reporting
    const reportButtons = document.querySelectorAll('.comment-report');
    reportButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            reportComment(commentId);
        });
    });
}

function handleCommentVote(commentId, voteType, button) {
    // AJAX call to handle voting
    const formData = new FormData();
    formData.append('action', 'comment_vote');
    formData.append('comment_id', commentId);
    formData.append('vote_type', voteType);
    formData.append('nonce', videoPlayerAjax.nonce);
    
    fetch(videoPlayerAjax.ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update vote count
            const countSpan = button.querySelector('.vote-count');
            countSpan.textContent = data.data.count;
            
            // Update button state
            button.classList.toggle('voted');
        }
    })
    .catch(error => {
        console.error('Error voting on comment:', error);
    });
}

function shareComment(commentId) {
    const commentElement = document.getElementById('comment-' + commentId);
    const commentUrl = window.location.href + '#comment-' + commentId;
    
    if (navigator.share) {
        navigator.share({
            title: 'Comentario interesante',
            url: commentUrl
        });
    } else {
        navigator.clipboard.writeText(commentUrl);
        alert('¡Enlace del comentario copiado!');
    }
}

function reportComment(commentId) {
    if (confirm('¿Estás seguro de que quieres reportar este comentario?')) {
        const formData = new FormData();
        formData.append('action', 'report_comment');
        formData.append('comment_id', commentId);
        formData.append('nonce', videoPlayerAjax.nonce);
        
        fetch(videoPlayerAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Comentario reportado. Gracias por mantener la comunidad segura.');
            }
        })
        .catch(error => {
            console.error('Error reporting comment:', error);
        });
    }
}

function sortComments(sortBy) {
    // Implementation for sorting comments
    // This would typically reload the page with sort parameters
    const url = new URL(window.location);
    url.searchParams.set('comment_order', sortBy);
    window.location.href = url.toString();
}

function toggleCommentsView(view) {
    const toggles = document.querySelectorAll('.view-toggle');
    const commentsList = document.getElementById('comments-list');
    
    toggles.forEach(toggle => {
        toggle.classList.remove('active');
    });
    
    document.querySelector(`[data-view="${view}"]`).classList.add('active');
    
    if (view === 'flat') {
        commentsList.classList.add('flat-view');
    } else {
        commentsList.classList.remove('flat-view');
    }
}
</script>