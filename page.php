<?php
/**
 * Page template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
            
            <!-- Page Header -->
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
                
                <?php if (has_excerpt()) : ?>
                    <div class="page-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Page Meta -->
                <div class="page-meta">
                    <span class="page-date">
                        📅 <?php echo get_the_modified_date(); ?>
                    </span>
                    
                    <?php if (get_the_author()) : ?>
                        <span class="page-author">
                            👤 <?php the_author(); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php 
                    $reading_time = videoplayer_estimated_reading_time(get_the_content());
                    if ($reading_time > 0) :
                    ?>
                        <span class="reading-time">
                            ⏱️ <?php printf(esc_html__('%d min de lectura', 'videoplayer'), $reading_time); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="page-featured-image">
                    <?php the_post_thumbnail('large', array('loading' => 'eager')); ?>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="page-content-wrapper">
                <div class="page-content-inner">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Páginas:', 'videoplayer'),
                        'after'  => '</div>',
                        'pagelink' => '<span class="page-number">%</span>',
                    ));
                    ?>
                </div>
                
                <!-- Page Sidebar for Long Content -->
                <?php if (strlen(get_the_content()) > 2000) : ?>
                    <aside class="page-sidebar">
                        <div class="page-toc">
                            <h3><?php esc_html_e('Contenido', 'videoplayer'); ?></h3>
                            <div id="table-of-contents"></div>
                        </div>
                        
                        <div class="page-progress">
                            <div class="progress-label"><?php esc_html_e('Progreso de lectura', 'videoplayer'); ?></div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="reading-progress"></div>
                            </div>
                            <div class="progress-percentage" id="progress-percentage">0%</div>
                        </div>
                        
                        <div class="page-actions">
                            <button class="action-btn" onclick="window.print()" title="<?php esc_attr_e('Imprimir página', 'videoplayer'); ?>">
                                🖨️ <?php esc_html_e('Imprimir', 'videoplayer'); ?>
                            </button>
                            
                            <button class="action-btn" onclick="sharePage()" title="<?php esc_attr_e('Compartir página', 'videoplayer'); ?>">
                                📤 <?php esc_html_e('Compartir', 'videoplayer'); ?>
                            </button>
                            
                            <button class="action-btn" onclick="toggleDarkMode()" title="<?php esc_attr_e('Cambiar tema', 'videoplayer'); ?>" id="theme-toggle">
                                🌙 <?php esc_html_e('Tema', 'videoplayer'); ?>
                            </button>
                        </div>
                    </aside>
                <?php endif; ?>
            </div>

            <!-- Page Navigation -->
            <?php
            $prev_page = get_previous_post();
            $next_page = get_next_post();
            
            if ($prev_page || $next_page) :
            ?>
                <nav class="page-navigation" role="navigation" aria-label="<?php esc_attr_e('Navegación de páginas', 'videoplayer'); ?>">
                    <div class="nav-links">
                        <?php if ($prev_page) : ?>
                            <div class="nav-previous">
                                <a href="<?php echo get_permalink($prev_page); ?>" class="nav-link">
                                    <span class="nav-direction">← <?php esc_html_e('Anterior', 'videoplayer'); ?></span>
                                    <span class="nav-title"><?php echo get_the_title($prev_page); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_page) : ?>
                            <div class="nav-next">
                                <a href="<?php echo get_permalink($next_page); ?>" class="nav-link">
                                    <span class="nav-direction"><?php esc_html_e('Siguiente', 'videoplayer'); ?> →</span>
                                    <span class="nav-title"><?php echo get_the_title($next_page); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- Related Pages -->
            <?php
            $related_pages = get_pages(array(
                'parent' => wp_get_post_parent_id(get_the_ID()),
                'exclude' => get_the_ID(),
                'number' => 4,
                'sort_column' => 'menu_order, post_title',
            ));
            
            if (!empty($related_pages)) :
            ?>
                <section class="related-pages">
                    <h3 class="related-title"><?php esc_html_e('Páginas relacionadas', 'videoplayer'); ?></h3>
                    
                    <div class="related-pages-grid">
                        <?php foreach ($related_pages as $related_page) : ?>
                            <article class="related-page-card">
                                <div class="related-page-thumbnail">
                                    <a href="<?php echo get_permalink($related_page); ?>">
                                        <?php if (has_post_thumbnail($related_page)) : ?>
                                            <?php echo get_the_post_thumbnail($related_page, 'medium'); ?>
                                        <?php else : ?>
                                            <div class="thumbnail-placeholder">
                                                <span class="page-icon">📄</span>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                
                                <div class="related-page-content">
                                    <h4 class="related-page-title">
                                        <a href="<?php echo get_permalink($related_page); ?>">
                                            <?php echo get_the_title($related_page); ?>
                                        </a>
                                    </h4>
                                    
                                    <?php if ($related_page->post_excerpt) : ?>
                                        <p class="related-page-excerpt">
                                            <?php echo wp_trim_words($related_page->post_excerpt, 15); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="related-page-meta">
                                        <?php echo get_the_modified_date('j M Y', $related_page); ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Contact Form for Contact Page -->
            <?php if (is_page('contact') || is_page('contacto')) : ?>
                <section class="contact-form-section">
                    <h3><?php esc_html_e('Envíanos un mensaje', 'videoplayer'); ?></h3>
                    
                    <form class="contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <input type="hidden" name="action" value="submit_contact_form">
                        <?php wp_nonce_field('contact_form_nonce', 'contact_nonce'); ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-name"><?php esc_html_e('Nombre *', 'videoplayer'); ?></label>
                                <input type="text" id="contact-name" name="contact_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-email"><?php esc_html_e('Email *', 'videoplayer'); ?></label>
                                <input type="email" id="contact-email" name="contact_email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-subject"><?php esc_html_e('Asunto', 'videoplayer'); ?></label>
                            <input type="text" id="contact-subject" name="contact_subject">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-message"><?php esc_html_e('Mensaje *', 'videoplayer'); ?></label>
                            <textarea id="contact-message" name="contact_message" rows="6" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="submit-btn">
                                <?php esc_html_e('Enviar Mensaje', 'videoplayer'); ?>
                            </button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

        </article>

    <?php endwhile; ?>
    
    <?php
    // If comments are open or there are comments, load the comments template
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
    ?>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-content {
    background: rgba(255, 255, 255, 0.02);
    border-radius: var(--border-radius);
    overflow: hidden;
    margin-bottom: 40px;
    border: 1px solid var(--border-color);
}

.page-header {
    text-align: center;
    padding: 50px 30px 30px;
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(78, 205, 196, 0.1));
    border-bottom: 1px solid var(--border-color);
}

.page-title {
    font-size: 3rem;
    margin-bottom: 20px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.page-excerpt {
    font-size: 1.2rem;
    color: var(--muted-text);
    max-width: 600px;
    margin: 0 auto 25px;
    line-height: 1.6;
}

.page-meta {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: wrap;
    font-size: 14px;
    color: var(--muted-text);
}

.page-featured-image {
    text-align: center;
    background: var(--darker-bg);
}

.page-featured-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.page-content-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 40px;
    padding: 40px 30px;
}

.page-content-inner {
    color: var(--light-text);
    line-height: 1.8;
    font-size: 16px;
}

.page-content-inner h2,
.page-content-inner h3,
.page-content-inner h4 {
    color: var(--primary-color);
    margin-top: 40px;
    margin-bottom: 20px;
    scroll-margin-top: 100px;
}

.page-content-inner h2 {
    font-size: 2rem;
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 10px;
}

.page-content-inner h3 {
    font-size: 1.5rem;
}

.page-content-inner h4 {
    font-size: 1.25rem;
}

.page-content-inner p {
    margin-bottom: 20px;
}

.page-content-inner a {
    color: var(--primary-color);
    text-decoration: underline;
    transition: var(--transition);
}

.page-content-inner a:hover {
    color: var(--secondary-color);
}

.page-content-inner ul,
.page-content-inner ol {
    margin-bottom: 20px;
    padding-left: 30px;
}

.page-content-inner li {
    margin-bottom: 8px;
}

.page-content-inner blockquote {
    background: rgba(255, 255, 255, 0.05);
    border-left: 4px solid var(--primary-color);
    padding: 20px 25px;
    margin: 30px 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
}

.page-content-inner code {
    background: rgba(255, 255, 255, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: var(--secondary-color);
}

.page-content-inner pre {
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 20px 0;
    border: 1px solid var(--border-color);
}

.page-content-inner pre code {
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

/* Page Sidebar */
.page-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 25px;
}

.page-toc h3,
.page-progress .progress-label {
    color: var(--primary-color);
    font-size: 16px;
    margin-bottom: 15px;
}

.page-toc ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-toc li {
    margin-bottom: 8px;
}

.page-toc a {
    color: var(--muted-text);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition);
    display: block;
    padding: 5px 0;
}

.page-toc a:hover,
.page-toc a.active {
    color: var(--primary-color);
    padding-left: 10px;
}

.page-progress {
    margin: 30px 0;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    margin: 10px 0;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 3px;
    width: 0%;
    transition: width 0.3s ease;
}

.progress-percentage {
    text-align: center;
    font-size: 12px;
    color: var(--muted-text);
}

.page-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    color: var(--muted-text);
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    text-align: left;
}

.action-btn:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Page Navigation */
.page-navigation {
    margin: 40px 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.nav-link {
    display: block;
    padding: 25px;
    text-decoration: none;
    transition: var(--transition);
    border-right: 1px solid var(--border-color);
}

.nav-next .nav-link {
    border-right: none;
    text-align: right;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
}

.nav-direction {
    display: block;
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-title {
    display: block;
    color: var(--light-text);
    font-weight: 600;
    line-height: 1.3;
}

/* Related Pages */
.related-pages {
    margin: 40px 0;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.related-title {
    color: var(--primary-color);
    margin-bottom: 25px;
    text-align: center;
}

.related-pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.related-page-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.related-page-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.08);
}

.related-page-thumbnail {
    height: 150px;
    background: var(--darker-bg);
}

.related-page-thumbnail img {
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

.page-icon {
    font-size: 3rem;
    opacity: 0.6;
}

.related-page-content {
    padding: 20px;
}

.related-page-title {
    font-size: 16px;
    margin-bottom: 10px;
}

.related-page-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.related-page-title a:hover {
    color: var(--primary-color);
}

.related-page-excerpt {
    color: var(--muted-text);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 10px;
}

.related-page-meta {
    font-size: 12px;
    color: var(--muted-text);
}

/* Contact Form */
.contact-form-section {
    margin: 40px 0;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.contact-form-section h3 {
    color: var(--primary-color);
    margin-bottom: 25px;
    text-align: center;
}

.contact-form {
    max-width: 600px;
    margin: 0 auto;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--light-text);
    font-weight: 500;
}

.form-group input,
.form-group textarea {
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

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.submit-btn {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

/* Responsive */
@media (min-width: 1024px) {
    .page-content-wrapper {
        grid-template-columns: 1fr 300px;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2.5rem;
    }
    
    .page-header {
        padding: 30px 20px 20px;
    }
    
    .page-content-wrapper {
        padding: 30px 20px;
    }
    
    .page-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .nav-links {
        grid-template-columns: 1fr;
    }
    
    .nav-next .nav-link {
        border-right: none;
        border-top: 1px solid var(--border-color);
        text-align: left;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .related-pages-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 2rem;
    }
    
    .page-content-inner {
        font-size: 15px;
    }
}

/* Print Styles */
@media print {
    .page-sidebar,
    .page-navigation,
    .related-pages,
    .contact-form-section {
        display: none;
    }
    
    .page-content {
        background: white;
        border: none;
        box-shadow: none;
    }
    
    .page-header {
        background: none;
        border-bottom: 2px solid #000;
    }
    
    .page-title {
        color: #000 !important;
        background: none !important;
        -webkit-text-fill-color: unset !important;
    }
    
    .page-content-inner {
        color: #000;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate Table of Contents
    generateTableOfContents();
    
    // Reading Progress
    updateReadingProgress();
    
    // Smooth scroll for TOC links
    setupSmoothScrolling();
    
    // Auto-save form drafts
    setupFormDrafts();
    
    // Enhanced typography
    enhanceTypography();
    
    function generateTableOfContents() {
        const tocContainer = document.getElementById('table-of-contents');
        if (!tocContainer) return;
        
        const headings = document.querySelectorAll('.page-content-inner h2, .page-content-inner h3, .page-content-inner h4');
        if (headings.length === 0) return;
        
        const tocList = document.createElement('ul');
        tocList.className = 'toc-list';
        
        headings.forEach((heading, index) => {
            const id = `heading-${index}`;
            heading.id = id;
            
            const li = document.createElement('li');
            li.className = `toc-level-${heading.tagName.toLowerCase()}`;
            
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent;
            link.className = 'toc-link';
            
            li.appendChild(link);
            tocList.appendChild(li);
        });
        
        tocContainer.appendChild(tocList);
    }
    
    function updateReadingProgress() {
        const progressFill = document.getElementById('reading-progress');
        const progressPercentage = document.getElementById('progress-percentage');
        
        if (!progressFill || !progressPercentage) return;
        
        function updateProgress() {
            const content = document.querySelector('.page-content-inner');
            if (!content) return;
            
            const contentTop = content.offsetTop;
            const contentHeight = content.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;
            
            const scrollPercent = Math.max(0, Math.min(100, 
                ((scrollTop - contentTop + windowHeight) / contentHeight) * 100
            ));
            
            progressFill.style.width = scrollPercent + '%';
            progressPercentage.textContent = Math.round(scrollPercent) + '%';
            
            // Update active TOC link
            updateActiveTocLink();
        }
        
        window.addEventListener('scroll', updateProgress);
        window.addEventListener('resize', updateProgress);
        updateProgress();
    }
    
    function updateActiveTocLink() {
        const tocLinks = document.querySelectorAll('.toc-link');
        const headings = document.querySelectorAll('.page-content-inner h2, .page-content-inner h3, .page-content-inner h4');
        
        let activeHeading = null;
        const scrollTop = window.pageYOffset + 150;
        
        headings.forEach(heading => {
            if (heading.offsetTop <= scrollTop) {
                activeHeading = heading;
            }
        });
        
        tocLinks.forEach(link => link.classList.remove('active'));
        
        if (activeHeading) {
            const activeLink = document.querySelector(`a[href="#${activeHeading.id}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }
    }
    
    function setupSmoothScrolling() {
        const tocLinks = document.querySelectorAll('.toc-link');
        
        tocLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    function setupFormDrafts() {
        const contactForm = document.querySelector('.contact-form');
        if (!contactForm) return;
        
        const formFields = contactForm.querySelectorAll('input, textarea');
        const formKey = 'contact_form_draft_' + window.location.pathname;
        
        // Load saved draft
        const savedData = localStorage.getItem(formKey);
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                formFields.forEach(field => {
                    if (formData[field.name]) {
                        field.value = formData[field.name];
                    }
                });
            } catch (e) {
                console.error('Error loading form draft:', e);
            }
        }
        
        // Save draft on input
        function saveDraft() {
            const formData = {};
            formFields.forEach(field => {
                if (field.value.trim()) {
                    formData[field.name] = field.value;
                }
            });
            
            if (Object.keys(formData).length > 0) {
                localStorage.setItem(formKey, JSON.stringify(formData));
            }
        }
        
        formFields.forEach(field => {
            field.addEventListener('input', saveDraft);
        });
        
        // Clear draft on successful submission
        contactForm.addEventListener('submit', function() {
            setTimeout(() => {
                localStorage.removeItem(formKey);
            }, 1000);
        });
    }
    
    function enhanceTypography() {
        // Add reading indicators
        const content = document.querySelector('.page-content-inner');
        if (!content) return;
        
        // Highlight code blocks
        const codeBlocks = content.querySelectorAll('pre code');
        codeBlocks.forEach(block => {
            block.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent).then(() => {
                    // Show temporary feedback
                    const feedback = document.createElement('span');
                    feedback.textContent = '✓ Copiado';
                    feedback.style.cssText = `
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        background: var(--primary-color);
                        color: white;
                        padding: 5px 10px;
                        border-radius: 4px;
                        font-size: 12px;
                        z-index: 10;
                    `;
                    
                    this.parentElement.style.position = 'relative';
                    this.parentElement.appendChild(feedback);
                    
                    setTimeout(() => {
                        feedback.remove();
                    }, 2000);
                });
            });
            
            block.style.cursor = 'pointer';
            block.title = 'Click para copiar';
        });
        
        // Add copy button to code blocks
        const preBlocks = content.querySelectorAll('pre');
        preBlocks.forEach(pre => {
            const copyBtn = document.createElement('button');
            copyBtn.textContent = '📋';
            copyBtn.className = 'copy-code-btn';
            copyBtn.style.cssText = `
                position: absolute;
                top: 10px;
                right: 10px;
                background: rgba(255, 255, 255, 0.1);
                border: none;
                color: white;
                padding: 5px 8px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
                transition: var(--transition);
            `;
            
            copyBtn.addEventListener('click', function() {
                const code = pre.querySelector('code');
                if (code) {
                    navigator.clipboard.writeText(code.textContent).then(() => {
                        this.textContent = '✓';
                        setTimeout(() => {
                            this.textContent = '📋';
                        }, 2000);
                    });
                }
            });
            
            pre.style.position = 'relative';
            pre.appendChild(copyBtn);
        });
    }
});

// Global functions for buttons
function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Enlace copiado al portapapeles');
        });
    }
}

function toggleDarkMode() {
    document.body.classList.toggle('light-mode');
    const isDarkMode = !document.body.classList.contains('light-mode');
    localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
    
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.innerHTML = isDarkMode ? '🌙 Tema' : '☀️ Tema';
    }
}

// Load theme preference
document.addEventListener('DOMContentLoaded', function() {
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'false') {
        document.body.classList.add('light-mode');
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.innerHTML = '☀️ Tema';
        }
    }
});
</script>

<?php
/**
 * Estimated reading time function
 */
function videoplayer_estimated_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return $reading_time;
}
?>

<?php get_footer(); ?>