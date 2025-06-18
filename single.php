<?php
/**
 * Single Post Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
            
            <!-- Post Header -->
            <header class="post-header">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="post-header-content">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    
                    <div class="post-meta">
                        <div class="meta-primary">
                            <span class="post-date">
                                📅 <?php echo get_the_date(); ?>
                            </span>
                            
                            <span class="post-author">
                                👤 Por <?php the_author_posts_link(); ?>
                            </span>
                            
                            <?php
                            $reading_time = videoplayer_estimated_reading_time(get_the_content());
                            if ($reading_time > 0) :
                            ?>
                                <span class="reading-time">
                                    ⏱️ <?php printf(esc_html__('%d min de lectura', 'videoplayer'), $reading_time); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="meta-secondary">
                            <?php if (has_category()) : ?>
                                <span class="post-categories">
                                    📁 <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (has_tag()) : ?>
                                <span class="post-tags">
                                    🏷️ <?php the_tags('', ', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Post Content -->
            <div class="post-content-wrapper">
                <div class="post-content">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Páginas:', 'videoplayer'),
                        'after'  => '</div>',
                        'pagelink' => '<span class="page-number">%</span>',
                    ));
                    ?>
                </div>
                
                <!-- Social Share -->
                <div class="post-social-share">
                    <h3 class="share-title"><?php esc_html_e('Compartir este artículo:', 'videoplayer'); ?></h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                           class="share-btn facebook" target="_blank" rel="noopener">
                            <span class="icon">📘</span>
                            <span class="text">Facebook</span>
                        </a>
                        
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                           class="share-btn twitter" target="_blank" rel="noopener">
                            <span class="icon">🐦</span>
                            <span class="text">Twitter</span>
                        </a>
                        
                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" 
                           class="share-btn whatsapp" target="_blank" rel="noopener">
                            <span class="icon">💬</span>
                            <span class="text">WhatsApp</span>
                        </a>
                        
                        <button class="share-btn copy-link" onclick="copyToClipboard('<?php echo get_permalink(); ?>')">
                            <span class="icon">🔗</span>
                            <span class="text"><?php esc_html_e('Copiar enlace', 'videoplayer'); ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Post Navigation -->
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            
            if ($prev_post || $next_post) :
            ?>
                <nav class="post-navigation" role="navigation" aria-label="<?php esc_attr_e('Navegación de artículos', 'videoplayer'); ?>">
                    <div class="nav-links">
                        <?php if ($prev_post) : ?>
                            <div class="nav-previous">
                                <a href="<?php echo get_permalink($prev_post); ?>" class="nav-link">
                                    <div class="nav-direction">← <?php esc_html_e('Artículo anterior', 'videoplayer'); ?></div>
                                    <div class="nav-title"><?php echo get_the_title($prev_post); ?></div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_post) : ?>
                            <div class="nav-next">
                                <a href="<?php echo get_permalink($next_post); ?>" class="nav-link">
                                    <div class="nav-direction"><?php esc_html_e('Siguiente artículo', 'videoplayer'); ?> →</div>
                                    <div class="nav-title"><?php echo get_the_title($next_post); ?></div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- Author Bio -->
            <?php if (get_the_author_meta('description')) : ?>
                <section class="author-bio">
                    <div class="author-avatar">
                        <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                    </div>
                    
                    <div class="author-info">
                        <h3 class="author-name">
                            <?php the_author_posts_link(); ?>
                        </h3>
                        
                        <div class="author-description">
                            <?php echo wpautop(get_the_author_meta('description')); ?>
                        </div>
                        
                        <div class="author-links">
                            <?php if (get_the_author_meta('url')) : ?>
                                <a href="<?php the_author_meta('url'); ?>" class="author-website" target="_blank" rel="noopener">
                                    🌐 <?php esc_html_e('Sitio web', 'videoplayer'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="author-posts">
                                📝 <?php esc_html_e('Ver todos los artículos', 'videoplayer'); ?>
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Related Posts -->
            <?php
            $related_posts = new WP_Query(array(
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID()),
                'orderby' => 'rand',
                'category__in' => wp_get_post_categories(get_the_ID())
            ));
            ?>

            <?php if ($related_posts->have_posts()) : ?>
                <section class="related-posts">
                    <h2 class="section-title"><?php esc_html_e('Artículos relacionados', 'videoplayer'); ?></h2>
                    
                    <div class="related-posts-grid">
                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                            <article class="related-post-card">
                                <div class="related-post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium'); ?>
                                        <?php else : ?>
                                            <div class="thumbnail-placeholder">
                                                <span class="post-icon">📄</span>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                
                                <div class="related-post-content">
                                    <h3 class="related-post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="related-post-meta">
                                        <span class="date"><?php echo get_the_date(); ?></span>
                                        <span class="reading-time"><?php echo videoplayer_estimated_reading_time(get_the_content()); ?> min</span>
                                    </div>
                                    
                                    <?php if (has_excerpt()) : ?>
                                        <div class="related-post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </section>
            <?php endif; ?>

        </article>

    <?php endwhile; ?>

    <!-- Comments Section -->
    <?php
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
    ?>
</div>

<!-- Sidebar -->
<?php if (is_active_sidebar('sidebar-1')) : ?>
    <aside class="post-sidebar">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </aside>
<?php endif; ?>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.single-post {
    max-width: 800px;
    margin: 0 auto;
}

.post-header {
    margin-bottom: 40px;
}

.post-featured-image {
    margin-bottom: 30px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.post-featured-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.post-header-content {
    text-align: center;
}

.post-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
    line-height: 1.2;
    color: var(--light-text);
}

.post-meta {
    display: flex;
    flex-direction: column;
    gap: 15px;
    font-size: 14px;
    color: var(--muted-text);
}

.meta-primary,
.meta-secondary {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
}

.meta-secondary a {
    color: var(--primary-color);
    text-decoration: none;
    transition: var(--transition);
}

.meta-secondary a:hover {
    color: var(--secondary-color);
}

.post-content-wrapper {
    background: rgba(255, 255, 255, 0.02);
    border-radius: var(--border-radius);
    padding: 40px 30px;
    margin-bottom: 40px;
    border: 1px solid var(--border-color);
}

.post-content {
    color: var(--light-text);
    line-height: 1.8;
    font-size: 16px;
    margin-bottom: 40px;
}

.post-content h2,
.post-content h3,
.post-content h4 {
    color: var(--primary-color);
    margin-top: 40px;
    margin-bottom: 20px;
}

.post-content h2 {
    font-size: 2rem;
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 10px;
}

.post-content h3 {
    font-size: 1.5rem;
}

.post-content h4 {
    font-size: 1.25rem;
}

.post-content p {
    margin-bottom: 20px;
}

.post-content a {
    color: var(--primary-color);
    text-decoration: underline;
    transition: var(--transition);
}

.post-content a:hover {
    color: var(--secondary-color);
}

.post-content ul,
.post-content ol {
    margin-bottom: 20px;
    padding-left: 30px;
}

.post-content li {
    margin-bottom: 8px;
}

.post-content blockquote {
    background: rgba(255, 255, 255, 0.05);
    border-left: 4px solid var(--primary-color);
    padding: 20px 25px;
    margin: 30px 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
}

.post-content code {
    background: rgba(255, 255, 255, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: var(--secondary-color);
}

.post-content pre {
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 20px 0;
    border: 1px solid var(--border-color);
}

.post-content pre code {
    background: none;
    padding: 0;
    color: var(--light-text);
}

.page-links {
    text-align: center;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
}

.page-number {
    display: inline-block;
    background: var(--hover-bg);
    color: var(--light-text);
    padding: 8px 15px;
    margin: 0 5px;
    border-radius: 6px;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.page-number:hover,
.page-number.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.post-social-share {
    border-top: 1px solid var(--border-color);
    padding-top: 30px;
}

.share-title {
    font-size: 18px;
    margin-bottom: 20px;
    color: var(--light-text);
}

.share-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.share-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
    font-size: 14px;
    cursor: pointer;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.share-btn.facebook:hover { background: rgba(24, 119, 242, 0.2); }
.share-btn.twitter:hover { background: rgba(29, 161, 242, 0.2); }
.share-btn.whatsapp:hover { background: rgba(37, 211, 102, 0.2); }
.share-btn.copy-link:hover { background: rgba(255, 255, 255, 0.2); }

.post-navigation {
    margin: 40px 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr;
}

.nav-link {
    display: block;
    padding: 25px;
    text-decoration: none;
    transition: var(--transition);
    border-bottom: 1px solid var(--border-color);
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
}

.nav-links .nav-next .nav-link {
    border-bottom: none;
}

.nav-direction {
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-title {
    color: var(--light-text);
    font-weight: 600;
    line-height: 1.3;
}

.author-bio {
    display: flex;
    gap: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 30px;
    margin: 40px 0;
    border: 1px solid var(--border-color);
}

.author-avatar img {
    border-radius: 50%;
    border: 2px solid var(--border-color);
}

.author-info {
    flex: 1;
}

.author-name {
    margin-bottom: 15px;
}

.author-name a {
    color: var(--primary-color);
    text-decoration: none;
    transition: var(--transition);
}

.author-name a:hover {
    color: var(--secondary-color);
}

.author-description {
    color: var(--muted-text);
    line-height: 1.6;
    margin-bottom: 20px;
}

.author-links {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.author-links a {
    color: var(--muted-text);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition);
}

.author-links a:hover {
    color: var(--primary-color);
}

.related-posts {
    margin: 40px 0;
}

.section-title {
    font-size: 2rem;
    margin-bottom: 30px;
    color: var(--primary-color);
    text-align: center;
}

.related-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.related-post-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.related-post-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.08);
}

.related-post-thumbnail {
    height: 150px;
    background: var(--darker-bg);
}

.related-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
}

.post-icon {
    font-size: 3rem;
    opacity: 0.6;
}

.related-post-content {
    padding: 20px;
}

.related-post-title {
    font-size: 16px;
    margin-bottom: 10px;
}

.related-post-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.related-post-title a:hover {
    color: var(--primary-color);
}

.related-post-meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 10px;
}

.related-post-excerpt {
    color: var(--muted-text);
    font-size: 14px;
    line-height: 1.5;
}

@media (min-width: 768px) {
    .nav-links {
        grid-template-columns: 1fr 1fr;
    }
    
    .nav-links .nav-next .nav-link {
        border-bottom: 1px solid var(--border-color);
        border-left: 1px solid var(--border-color);
        text-align: right;
    }
    
    .author-bio {
        align-items: center;
    }
}

@media (max-width: 768px) {
    .post-title {
        font-size: 2rem;
    }
    
    .post-content-wrapper {
        padding: 30px 20px;
    }
    
    .meta-primary,
    .meta-secondary {
        flex-direction: column;
        gap: 10px;
    }
    
    .share-buttons {
        flex-direction: column;
    }
    
    .author-bio {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .post-title {
        font-size: 1.5rem;
    }
    
    .post-content {
        font-size: 15px;
    }
    
    .related-posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Social share tracking
    const shareButtons = document.querySelectorAll('.share-btn');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.classList[1] || 'unknown';
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'share', {
                    'method': platform,
                    'content_type': 'article',
                    'content_id': '<?php echo get_the_ID(); ?>'
                });
            }
        });
    });
    
    // Reading progress
    const progressBar = document.createElement('div');
    progressBar.className = 'reading-progress';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: var(--gradient-primary);
        z-index: 1000;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(progressBar);
    
    function updateReadingProgress() {
        const article = document.querySelector('.post-content');
        if (!article) return;
        
        const articleTop = article.offsetTop;
        const articleHeight = article.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrollTop = window.pageYOffset;
        
        const progress = Math.max(0, Math.min(100, 
            ((scrollTop - articleTop + windowHeight) / articleHeight) * 100
        ));
        
        progressBar.style.width = progress + '%';
    }
    
    window.addEventListener('scroll', updateReadingProgress);
    updateReadingProgress();
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('¡Enlace copiado al portapapeles!');
    }).catch(err => {
        console.error('Error al copiar: ', err);
        // Fallback method
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('¡Enlace copiado al portapapeles!');
    });
}

function videoplayer_estimated_reading_time(content) {
    const wordsPerMinute = 200;
    const textContent = content.replace(/<[^>]*>/g, '');
    const wordCount = textContent.split(/\s+/).length;
    return Math.ceil(wordCount / wordsPerMinute);
}
</script>

<?php get_footer(); ?>