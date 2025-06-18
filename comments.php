<?php
/**
 * Comments template
 * 
 * @package VideoPlayerMobile
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    
    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ($comment_count == 1) {
                printf(
                    esc_html__('Un comentario en "%s"', 'videoplayer'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx('%1$s comentario en "%2$s"', '%1$s comentarios en "%2$s"', $comment_count, 'comments title', 'videoplayer')),
                    number_format_i18n($comment_count),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h3>

        <!-- Comments Navigation (Top) -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
                <h4 class="sr-only"><?php esc_html_e('Navegación de comentarios', 'videoplayer'); ?></h4>
                <div class="nav-links">
                    <div class="nav-previous">
                        <?php previous_comments_link(esc_html__('&larr; Comentarios más antiguos', 'videoplayer')); ?>
                    </div>
                    <div class="nav-next">
                        <?php next_comments_link(esc_html__('Comentarios más nuevos &rarr;', 'videoplayer')); ?>
                    </div>
                </div>
            </nav>
        <?php endif; ?>

        <!-- Comments List -->
        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'videoplayer_comment_callback',
            ));
            ?>
        </ol>

        <!-- Comments Navigation (Bottom) -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                <h4 class="sr-only"><?php esc_html_e('Navegación de comentarios', 'videoplayer'); ?></h4>
                <div class="nav-links">
                    <div class="nav-previous">
                        <?php previous_comments_link(esc_html__('&larr; Comentarios más antiguos', 'videoplayer')); ?>
                    </div>
                    <div class="nav-next">
                        <?php next_comments_link(esc_html__('Comentarios más nuevos &rarr;', 'videoplayer')); ?>
                    </div>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; // Check for have_comments(). ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note.
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
    ?>
        <p class="no-comments"><?php esc_html_e('Los comentarios están cerrados.', 'videoplayer'); ?></p>
    <?php endif; ?>

    <?php
    // Comment form
    if (comments_open()) :
        $comment_form_args = array(
            'title_reply'          => esc_html__('Deja tu comentario', 'videoplayer'),
            'title_reply_to'       => esc_html__('Responder a %s', 'videoplayer'),
            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
            'title_reply_after'    => '</h3>',
            'cancel_reply_before'  => ' <small>',
            'cancel_reply_after'   => '</small>',
            'cancel_reply_link'    => esc_html__('Cancelar respuesta', 'videoplayer'),
            'label_submit'         => esc_html__('Publicar Comentario', 'videoplayer'),
            'submit_button'        => '<button type="submit" id="%2$s" class="%3$s">%4$s</button>',
            'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
            'format'               => 'xhtml',
            'comment_field'        => '<p class="comment-form-comment">
                                        <label for="comment">' . esc_html__('Comentario', 'videoplayer') . ' <span class="required">*</span></label>
                                        <textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required="required" placeholder="' . esc_attr__('Escribe tu comentario aquí...', 'videoplayer') . '"></textarea>
                                      </p>',
            'must_log_in'          => '<p class="must-log-in">' . 
                                      sprintf(
                                          wp_kses(
                                              __('Debes <a href="%s">iniciar sesión</a> para publicar un comentario.', 'videoplayer'),
                                              array(
                                                  'a' => array(
                                                      'href' => array(),
                                                  ),
                                              )
                                          ),
                                          wp_login_url(apply_filters('the_permalink', get_permalink(get_the_ID())))
                                      ) . '</p>',
            'logged_in_as'         => '<p class="logged-in-as">' . 
                                      sprintf(
                                          wp_kses(
                                              __('Conectado como <a href="%1$s">%2$s</a>. <a href="%3$s" title="Cerrar sesión">¿Cerrar sesión?</a>', 'videoplayer'),
                                              array(
                                                  'a' => array(
                                                      'href'  => array(),
                                                      'title' => array(),
                                                  ),
                                              )
                                          ),
                                          get_edit_user_link(),
                                          $user_identity,
                                          wp_logout_url(apply_filters('the_permalink', get_permalink(get_the_ID())))
                                      ) . '</p>',
            'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . 
                                      esc_html__('Tu dirección de correo electrónico no será publicada.', 'videoplayer') . '</span>' . 
                                      ' <span class="required-field-message">' . 
                                      esc_html__('Los campos requeridos están marcados con *', 'videoplayer') . '</span></p>',
            'comment_notes_after'  => '',
            'id_form'              => 'commentform',
            'id_submit'            => 'submit',
            'class_container'      => 'comment-respond',
            'class_form'           => 'comment-form',
            'class_submit'         => 'submit-comment-btn',
            'name_submit'          => 'submit',
            'fields'               => array(
                'author' => '<p class="comment-form-author">
                             <label for="author">' . esc_html__('Nombre', 'videoplayer') . ' <span class="required">*</span></label>
                             <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required="required" placeholder="' . esc_attr__('Tu nombre', 'videoplayer') . '" />
                           </p>',
                'email'  => '<p class="comment-form-email">
                             <label for="email">' . esc_html__('Email', 'videoplayer') . ' <span class="required">*</span></label>
                             <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" required="required" placeholder="' . esc_attr__('tu@email.com', 'videoplayer') . '" />
                           </p>',
                'url'    => '<p class="comment-form-url">
                             <label for="url">' . esc_html__('Web', 'videoplayer') . '</label>
                             <input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" placeholder="' . esc_attr__('https://tusitio.com (opcional)', 'videoplayer') . '" />
                           </p>',
                'cookies' => '<p class="comment-form-cookies-consent">
                              <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . (empty($commenter['comment_author_email']) ? '' : ' checked="checked"') . ' />
                              <label for="wp-comment-cookies-consent">' . esc_html__('Guardar mi nombre, email y sitio web para la próxima vez que comente.', 'videoplayer') . '</label>
                            </p>',
            ),
        );

        comment_form($comment_form_args);
    endif;
    ?>

</div><!-- #comments -->

<style>
.comments-area {
    margin-top: 40px;
    padding: 30px 20px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.comments-title {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: var(--primary-color);
    text-align: center;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

/* Comment Navigation */
.comment-navigation {
    margin-bottom: 30px;
}

.comment-navigation .nav-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-navigation a {
    background: var(--hover-bg);
    color: var(--light-text);
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.comment-navigation a:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

/* Comment List */
.comment-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.comment-list .comment {
    margin-bottom: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.comment-list .comment:hover {
    background: rgba(255, 255, 255, 0.08);
}

.comment-body {
    padding: 20px;
}

.comment-author {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.comment-author .avatar {
    border-radius: 50%;
    margin-right: 15px;
    border: 2px solid var(--border-color);
}

.comment-author .fn {
    font-weight: 600;
    color: var(--primary-color);
    text-decoration: none;
    margin-right: 10px;
}

.comment-author .says {
    display: none;
}

.comment-metadata {
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 15px;
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
    line-height: 1.6;
    margin-bottom: 15px;
}

.comment-content p {
    margin-bottom: 15px;
}

.comment-content p:last-child {
    margin-bottom: 0;
}

.reply {
    text-align: right;
}

.comment-reply-link {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.comment-reply-link:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Nested Comments */
.comment-list .children {
    list-style: none;
    margin: 20px 0 0 40px;
    padding: 0;
}

.comment-list .children .comment {
    border-left: 3px solid var(--primary-color);
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
}

/* Comment Awaiting Moderation */
.comment-awaiting-moderation {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 193, 7, 0.3);
    font-size: 14px;
}

/* Comment Form */
.comment-respond {
    margin-top: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.comment-reply-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.comment-reply-title small {
    font-size: 14px;
    font-weight: normal;
}

.comment-reply-title small a {
    color: var(--muted-text);
    text-decoration: none;
    transition: var(--transition);
}

.comment-reply-title small a:hover {
    color: var(--primary-color);
}

.comment-notes {
    color: var(--muted-text);
    font-size: 14px;
    margin-bottom: 20px;
    line-height: 1.5;
}

.required {
    color: var(--primary-color);
}

.comment-form {
    display: grid;
    gap: 20px;
}

.comment-form p {
    margin: 0;
}

.comment-form label {
    display: block;
    margin-bottom: 8px;
    color: var(--light-text);
    font-weight: 500;
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form input[type="url"],
.comment-form textarea {
    width: 100%;
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 12px 15px;
    color: var(--light-text);
    font-size: 14px;
    transition: var(--transition);
    font-family: inherit;
}

.comment-form input[type="text"]:focus,
.comment-form input[type="email"]:focus,
.comment-form input[type="url"]:focus,
.comment-form textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

.comment-form input::placeholder,
.comment-form textarea::placeholder {
    color: var(--muted-text);
}

.comment-form textarea {
    resize: vertical;
    min-height: 120px;
}

.comment-form-cookies-consent {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.comment-form-cookies-consent input[type="checkbox"] {
    width: auto;
    margin-top: 2px;
    accent-color: var(--primary-color);
}

.comment-form-cookies-consent label {
    margin-bottom: 0;
    font-size: 13px;
    line-height: 1.4;
}

.form-submit {
    text-align: center;
    margin-top: 10px;
}

.submit-comment-btn {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.submit-comment-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.submit-comment-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.submit-comment-btn.loading {
    color: transparent;
}

.submit-comment-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* No Comments */
.no-comments {
    text-align: center;
    color: var(--muted-text);
    font-style: italic;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

/* Must Log In */
.must-log-in {
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 30px;
    color: var(--muted-text);
}

.must-log-in a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.must-log-in a:hover {
    text-decoration: underline;
}

/* Logged In As */
.logged-in-as {
    background: rgba(78, 205, 196, 0.1);
    border: 1px solid rgba(78, 205, 196, 0.3);
    border-radius: var(--border-radius);
    padding: 15px;
    margin-bottom: 20px;
    color: var(--secondary-color);
    font-size: 14px;
}

.logged-in-as a {
    color: var(--secondary-color);
    text-decoration: none;
    font-weight: 600;
}

.logged-in-as a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (min-width: 768px) {
    .comment-form {
        grid-template-columns: 1fr 1fr;
        grid-template-areas: 
            "author email"
            "url url"
            "comment comment"
            "cookies cookies"
            "submit submit";
    }
    
    .comment-form-author { grid-area: author; }
    .comment-form-email { grid-area: email; }
    .comment-form-url { grid-area: url; }
    .comment-form-comment { grid-area: comment; }
    .comment-form-cookies-consent { grid-area: cookies; }
    .form-submit { grid-area: submit; }
}

@media (max-width: 768px) {
    .comment-list .children {
        margin-left: 20px;
    }
    
    .comment-author .avatar {
        width: 40px;
        height: 40px;
    }
    
    .comments-area {
        padding: 20px 15px;
    }
    
    .comment-respond {
        padding: 20px 15px;
    }
}

@media (max-width: 480px) {
    .comments-title {
        font-size: 1.5rem;
    }
    
    .comment-reply-title {
        font-size: 1.3rem;
    }
    
    .comment-list .children {
        margin-left: 10px;
    }
    
    .comment-navigation .nav-links {
        flex-direction: column;
        gap: 10px;
    }
}

/* Dark mode specific adjustments */
@media (prefers-color-scheme: dark) {
    .comment-form input[type="text"],
    .comment-form input[type="email"],
    .comment-form input[type="url"],
    .comment-form textarea {
        color-scheme: dark;
    }
}

/* Accessibility improvements */
.comment-form input:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.comment-form input:valid {
    border-color: #28a745;
}

/* Comment threading indicators */
.comment-list .depth-2 { padding-left: 20px; }
.comment-list .depth-3 { padding-left: 40px; }
.comment-list .depth-4 { padding-left: 60px; }
.comment-list .depth-5 { padding-left: 80px; }

@media (max-width: 768px) {
    .comment-list .depth-2 { padding-left: 10px; }
    .comment-list .depth-3 { padding-left: 20px; }
    .comment-list .depth-4 { padding-left: 30px; }
    .comment-list .depth-5 { padding-left: 40px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Comment form enhancements
    const commentForm = document.getElementById('commentform');
    const submitBtn = document.getElementById('submit');
    
    if (commentForm && submitBtn) {
        // Add loading state on form submission
        commentForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
        
        // Character counter for comment textarea
        const commentTextarea = document.getElementById('comment');
        if (commentTextarea) {
            const maxLength = commentTextarea.getAttribute('maxlength');
            if (maxLength) {
                const counter = document.createElement('div');
                counter.className = 'character-counter';
                counter.style.cssText = `
                    text-align: right;
                    color: var(--muted-text);
                    font-size: 12px;
                    margin-top: 5px;
                `;
                
                const updateCounter = () => {
                    const remaining = maxLength - commentTextarea.value.length;
                    counter.textContent = remaining + ' caracteres restantes';
                    
                    if (remaining < 100) {
                        counter.style.color = 'var(--primary-color)';
                    } else {
                        counter.style.color = 'var(--muted-text)';
                    }
                };
                
                commentTextarea.addEventListener('input', updateCounter);
                commentTextarea.parentNode.appendChild(counter);
                updateCounter();
            }
        }
        
        // Auto-resize textarea
        if (commentTextarea) {
            commentTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
        
        // Save comment draft
        const saveCommentDraft = () => {
            if (commentTextarea && commentTextarea.value.trim()) {
                localStorage.setItem('commentDraft_' + window.location.pathname, commentTextarea.value);
            }
        };
        
        const loadCommentDraft = () => {
            if (commentTextarea) {
                const draft = localStorage.getItem('commentDraft_' + window.location.pathname);
                if (draft) {
                    commentTextarea.value = draft;
                }
            }
        };
        
        // Load draft on page load
        loadCommentDraft();
        
        // Save draft periodically
        if (commentTextarea) {
            commentTextarea.addEventListener('input', saveCommentDraft);
        }
        
        // Clear draft on successful submission
        commentForm.addEventListener('submit', function() {
            setTimeout(() => {
                localStorage.removeItem('commentDraft_' + window.location.pathname);
            }, 1000);
        });
    }
    
    // Smooth scroll to comments on reply
    const replyLinks = document.querySelectorAll('.comment-reply-link');
    replyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            setTimeout(() => {
                const respondDiv = document.getElementById('respond');
                if (respondDiv) {
                    respondDiv.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 100);
        });
    });
    
    // Add animation to new comments
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('comment-submitted') === '1') {
        const comments = document.querySelectorAll('.comment');
        if (comments.length > 0) {
            const lastComment = comments[comments.length - 1];
            lastComment.style.animation = 'fadeInUp 0.6s ease-out';
            lastComment.scrollIntoView({ 
                behavior: 'smooth',
                block: 'center'
            });
        }
    }
    
    // Comment voting (if you want to add this feature)
    const addCommentVoting = () => {
        const comments = document.querySelectorAll('.comment-body');
        
        comments.forEach(comment => {
            const commentId = comment.closest('.comment').id.replace('comment-', '');
            
            const voteContainer = document.createElement('div');
            voteContainer.className = 'comment-voting';
            voteContainer.innerHTML = `
                <button class="vote-btn upvote" data-comment-id="${commentId}" data-vote="up">
                    👍 <span class="vote-count">0</span>
                </button>
                <button class="vote-btn downvote" data-comment-id="${commentId}" data-vote="down">
                    👎 <span class="vote-count">0</span>
                </button>
            `;
            
            // Add CSS for voting buttons
            voteContainer.style.cssText = `
                display: flex;
                gap: 10px;
                margin-top: 10px;
                align-items: center;
            `;
            
            const voteBtns = voteContainer.querySelectorAll('.vote-btn');
            voteBtns.forEach(btn => {
                btn.style.cssText = `
                    background: rgba(255, 255, 255, 0.1);
                    border: 1px solid var(--border-color);
                    color: var(--muted-text);
                    padding: 4px 8px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 12px;
                    transition: var(--transition);
                `;
                
                btn.addEventListener('click', function() {
                    // Here you would make an AJAX call to record the vote
                    console.log('Vote:', this.dataset.vote, 'Comment:', this.dataset.commentId);
                    
                    // Visual feedback
                    this.style.background = 'var(--primary-color)';
                    this.style.color = 'white';
                    
                    // Increment count (demo)
                    const countSpan = this.querySelector('.vote-count');
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                });
            });
            
            comment.appendChild(voteContainer);
        });
    };
    
    // Uncomment to enable comment voting
    // addCommentVoting();
    
    // Real-time comment updates (WebSocket example)
    const enableRealTimeComments = () => {
        // This would require WebSocket implementation on the server
        /*
        const ws = new WebSocket('wss://yoursite.com/comments');
        
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.type === 'new_comment' && data.post_id == getCurrentPostId()) {
                // Add new comment to the list
                addNewCommentToList(data.comment);
            }
        };
        */
    };
    
    // Comment moderation helpers (for administrators)
    if (document.body.classList.contains('admin-bar')) {
        addAdminCommentControls();
    }
    
    function addAdminCommentControls() {
        const comments = document.querySelectorAll('.comment');
        
        comments.forEach(comment => {
            const commentId = comment.id.replace('comment-', '');
            const adminControls = document.createElement('div');
            adminControls.className = 'admin-comment-controls';
            adminControls.style.cssText = `
                position: absolute;
                top: 10px;
                right: 10px;
                display: flex;
                gap: 5px;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            
            adminControls.innerHTML = `
                <button class="admin-btn approve" data-comment-id="${commentId}" title="Aprobar">✅</button>
                <button class="admin-btn delete" data-comment-id="${commentId}" title="Eliminar">❌</button>
            `;
            
            comment.style.position = 'relative';
            comment.appendChild(adminControls);
            
            comment.addEventListener('mouseenter', () => {
                adminControls.style.opacity = '1';
            });
            
            comment.addEventListener('mouseleave', () => {
                adminControls.style.opacity = '0';
            });
        });
    }
});
</script>

<?php
/**
 * Custom comment callback function
 */
function videoplayer_comment_callback($comment, $args, $depth) {
    $tag = ($args['style'] === 'div') ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['avatar_size']); ?>
                <span class="fn"><?php echo get_comment_author_link(); ?></span>
                <?php if (get_comment_author_url()) : ?>
                    <span class="author-badge">🌐</span>
                <?php endif; ?>
            </div>
            
            <div class="comment-metadata">
                <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                    <time datetime="<?php comment_time('c'); ?>">
                        <?php printf(esc_html__('%1$s a las %2$s', 'videoplayer'), get_comment_date(), get_comment_time()); ?>
                    </time>
                </a>
                <?php edit_comment_link(esc_html__('(Editar)', 'videoplayer'), '  ', ''); ?>
            </div>

            <?php if ($comment->comment_approved == '0') : ?>
                <em class="comment-awaiting-moderation">
                    <?php esc_html_e('Tu comentario está pendiente de moderación.', 'videoplayer'); ?>
                </em>
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <div class="reply">
                <?php comment_reply_link(array_merge($args, array(
                    'add_below' => 'comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth']
                ))); ?>
            </div>
        </div>
    <?php
    // Don't close the tag here - WordPress handles that
}
?>