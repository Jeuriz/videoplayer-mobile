<?php
/**
 * Single Video Template
 * 
 * @package VideoPlayerMobile
 */

get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-video'); ?>>
            
            <!-- Video Player Section -->
            <div class="video-container">
                <div class="video-player" id="main-video-player">
                    <?php 
                    $video_url = get_post_meta(get_the_ID(), '_video_url', true);
                    if ($video_url) : 
                    ?>
                        <video 
                            id="video-element"
                            class="actual-video" 
                            controls 
                            preload="metadata"
                            poster="<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'video-large') : ''; ?>"
                            <?php if (get_theme_mod('autoplay_videos', false)) : ?>autoplay muted<?php endif; ?>
                        >
                            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                            <p><?php esc_html_e('Tu navegador no soporta videos HTML5.', 'videoplayer'); ?></p>
                        </video>
                    <?php else : ?>
                        <!-- Placeholder or embedded video -->
                        <div class="video-placeholder" onclick="handleVideoClick(event)">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('video-large'); ?>
                            <?php else : ?>
                                <div class="placeholder-image">
                                    <span class="placeholder-icon">📹</span>
                                    <p><?php esc_html_e('Video no disponible', 'videoplayer'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="video-overlay">
                                <button class="play-btn" aria-label="<?php esc_attr_e('Reproducir video', 'videoplayer'); ?>">
                                    <div class="play-icon"></div>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Custom Video Controls -->
                    <div class="video-controls" id="custom-controls">
                        <div class="progress-container">
                            <div class="progress-bar" id="progress-bar">
                                <div class="progress-fill" id="progress-fill"></div>
                                <div class="progress-handle" id="progress-handle"></div>
                            </div>
                            <div class="time-display">
                                <span id="current-time">0:00</span> / <span id="duration">0:00</span>
                            </div>
                        </div>
                        
                        <div class="controls-row">
                            <div class="controls-left">
                                <button class="control-btn" id="play-pause-btn" aria-label="<?php esc_attr_e('Reproducir/Pausar', 'videoplayer'); ?>">
                                    <span class="icon">▶️</span>
                                </button>
                                
                                <div class="volume-control">
                                    <button class="control-btn" id="mute-btn" aria-label="<?php esc_attr_e('Silenciar', 'videoplayer'); ?>">
                                        <span class="icon">🔊</span>
                                    </button>
                                    <div class="volume-slider">
                                        <input type="range" id="volume-slider" min="0" max="100" value="100">
                                    </div>
                                </div>
                                
                                <div class="time-info">
                                    <span id="time-current">0:00</span>
                                    <span class="separator">/</span>
                                    <span id="time-total">0:00</span>
                                </div>
                            </div>
                            
                            <div class="controls-right">
                                <button class="control-btn" id="playback-speed" aria-label="<?php esc_attr_e('Velocidad de reproducción', 'videoplayer'); ?>">
                                    <span class="icon">⚡</span>
                                    <span class="speed-text">1x</span>
                                </button>
                                
                                <button class="control-btn" id="pip-btn" aria-label="<?php esc_attr_e('Picture-in-Picture', 'videoplayer'); ?>" style="display: none;">
                                    <span class="icon">📱</span>
                                </button>
                                
                                <button class="control-btn" id="fullscreen-btn" aria-label="<?php esc_attr_e('Pantalla completa', 'videoplayer'); ?>">
                                    <span class="icon">⛶</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Information -->
            <div class="video-info">
                <header class="video-header">
                    <h1 class="video-title"><?php the_title(); ?></h1>
                    
                    <div class="video-meta">
                        <div class="meta-primary">
                            <span class="views">👁️ <?php echo number_format(get_video_views()); ?> visualizaciones</span>
                            <span class="date">📅 <?php echo get_the_date(); ?></span>
                            <span class="duration">⏱️ <?php echo esc_html(get_video_duration()); ?></span>
                        </div>
                        
                        <div class="meta-secondary">
                            <span class="author">👤 Por <?php the_author_posts_link(); ?></span>
                            
                            <?php if (has_category()) : ?>
                                <span class="categories">
                                    📁 <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="action-btn like-btn" data-video-id="<?php the_ID(); ?>" aria-label="<?php esc_attr_e('Me gusta', 'videoplayer'); ?>">
                        <span class="icon">👍</span>
                        <span class="text"><?php esc_html_e('Me gusta', 'videoplayer'); ?></span>
                        <span class="count" id="like-count">0</span>
                    </button>
                    
                    <button class="action-btn share-btn" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>" aria-label="<?php esc_attr_e('Compartir', 'videoplayer'); ?>">
                        <span class="icon">📤</span>
                        <span class="text"><?php esc_html_e('Compartir', 'videoplayer'); ?></span>
                    </button>
                    
                    <button class="action-btn save-btn" data-video-id="<?php the_ID(); ?>" aria-label="<?php esc_attr_e('Guardar', 'videoplayer'); ?>">
                        <span class="icon">💾</span>
                        <span class="text"><?php esc_html_e('Guardar', 'videoplayer'); ?></span>
                    </button>
                    
                    <button class="action-btn report-btn" data-video-id="<?php the_ID(); ?>" aria-label="<?php esc_attr_e('Reportar', 'videoplayer'); ?>">
                        <span class="icon">🚩</span>
                        <span class="text"><?php esc_html_e('Reportar', 'videoplayer'); ?></span>
                    </button>
                </div>

                <!-- Video Description -->
                <?php if (has_excerpt() || get_the_content()) : ?>
                    <div class="video-description">
                        <div class="description-content" id="description-content">
                            <?php 
                            if (has_excerpt()) {
                                the_excerpt();
                            } else {
                                the_content();
                            }
                            ?>
                        </div>
                        
                        <?php if (strlen(get_the_content()) > 300) : ?>
                            <button class="description-toggle" id="description-toggle">
                                <?php esc_html_e('Ver más', 'videoplayer'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (has_tag()) : ?>
                    <div class="video-tags">
                        <h3 class="tags-title"><?php esc_html_e('Etiquetas:', 'videoplayer'); ?></h3>
                        <div class="tags-list">
                            <?php the_tags('<span class="tag">#', '</span><span class="tag">#', '</span>'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Related Videos -->
            <?php
            $related_videos = new WP_Query(array(
                'post_type' => 'video',
                'posts_per_page' => 8,
                'post__not_in' => array(get_the_ID()),
                'orderby' => 'rand',
                'meta_query' => array(
                    array(
                        'key' => '_view_count',
                        'compare' => 'EXISTS'
                    )
                )
            ));
            ?>

            <?php if ($related_videos->have_posts()) : ?>
                <section class="related-videos">
                    <h2 class="section-title"><?php esc_html_e('Videos Relacionados', 'videoplayer'); ?></h2>
                    
                    <div class="related-videos-grid">
                        <?php while ($related_videos->have_posts()) : $related_videos->the_post(); ?>
                            <article class="related-video-card">
                                <div class="related-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('video-thumbnail'); ?>
                                        <?php else : ?>
                                            <div class="thumbnail-placeholder">
                                                <span>▶</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="video-overlay">
                                            <div class="play-btn-small">▶</div>
                                        </div>
                                        
                                        <div class="video-duration"><?php echo esc_html(get_video_duration()); ?></div>
                                    </a>
                                </div>
                                
                                <div class="related-info">
                                    <h3 class="related-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="related-meta">
                                        <span class="views"><?php echo number_format(get_video_views()); ?> vistas</span>
                                        <span class="date"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                    </div>
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

<!-- Sidebar for desktop -->
<?php if (is_active_sidebar('video-sidebar')) : ?>
    <aside class="video-sidebar">
        <?php dynamic_sidebar('video-sidebar'); ?>
    </aside>
<?php endif; ?>

<style>
.single-video {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.video-container {
    background: #000;
    border-radius: var(--border-radius);
    overflow: hidden;
    margin-bottom: 20px;
    position: relative;
}

.video-player {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.actual-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.video-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.placeholder-image {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #333, #555);
    color: rgba(255, 255, 255, 0.7);
}

.placeholder-icon {
    font-size: 4rem;
    margin-bottom: 15px;
}

.video-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    padding: 20px 15px 15px;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.video-player:hover .video-controls {
    transform: translateY(0);
}

.progress-container {
    margin-bottom: 15px;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
    cursor: pointer;
    position: relative;
    margin-bottom: 8px;
}

.progress-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 3px;
    width: 0%;
    transition: width 0.1s ease;
}

.progress-handle {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    background: var(--primary-color);
    border-radius: 50%;
    left: 0%;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.progress-bar:hover .progress-handle {
    opacity: 1;
}

.controls-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.controls-left,
.controls-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.control-btn {
    background: none;
    border: none;
    color: var(--light-text);
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 5px;
}

.control-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.volume-control {
    display: flex;
    align-items: center;
    gap: 8px;
}

.volume-slider {
    width: 80px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.volume-control:hover .volume-slider {
    opacity: 1;
}

.volume-slider input {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    outline: none;
    cursor: pointer;
}

.time-info {
    font-size: 12px;
    color: var(--muted-text);
}

.separator {
    margin: 0 3px;
}

.video-info {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    padding: 25px;
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}

.video-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.video-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 15px;
    line-height: 1.3;
    color: var(--light-text);
}

.video-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.meta-primary,
.meta-secondary {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 14px;
}

.meta-primary {
    color: var(--muted-text);
}

.meta-secondary {
    color: var(--muted-text);
}

.meta-secondary a {
    color: var(--primary-color);
    text-decoration: none;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 12px 20px;
    color: var(--light-text);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.action-btn.liked {
    background: rgba(255, 107, 107, 0.2);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.video-description {
    margin-bottom: 25px;
}

.description-content {
    line-height: 1.6;
    color: var(--muted-text);
}

.description-toggle {
    background: none;
    border: none;
    color: var(--primary-color);
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    transition: var(--transition);
}

.description-toggle:hover {
    text-decoration: underline;
}

.video-tags {
    border-top: 1px solid var(--border-color);
    padding-top: 20px;
}

.tags-title {
    font-size: 16px;
    margin-bottom: 10px;
    color: var(--light-text);
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    text-decoration: none;
    transition: var(--transition);
}

.tag:hover {
    background: var(--primary-color);
    color: white;
}

.related-videos {
    margin-bottom: 40px;
}

.section-title {
    font-size: 20px;
    margin-bottom: 20px;
    color: var(--light-text);
}

.related-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.related-video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.related-video-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.08);
}

.related-thumbnail {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.related-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-info {
    padding: 15px;
}

.related-title {
    font-size: 14px;
    margin-bottom: 8px;
    line-height: 1.3;
}

.related-title a {
    color: var(--light-text);
    text-decoration: none;
    transition: var(--transition);
}

.related-title a:hover {
    color: var(--primary-color);
}

.related-meta {
    font-size: 12px;
    color: var(--muted-text);
    display: flex;
    flex-direction: column;
    gap: 4px;
}

@media (min-width: 768px) {
    .video-meta {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .video-title {
        font-size: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .controls-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .related-videos-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initVideoPlayer();
    initActionButtons();
    initDescriptionToggle();
});

function initVideoPlayer() {
    const video = document.getElementById('video-element');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const progressBar = document.getElementById('progress-bar');
    const progressFill = document.getElementById('progress-fill');
    const currentTimeEl = document.getElementById('current-time');
    const durationEl = document.getElementById('duration');
    const volumeSlider = document.getElementById('volume-slider');
    const muteBtn = document.getElementById('mute-btn');
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    
    if (!video) return;
    
    // Play/Pause
    playPauseBtn?.addEventListener('click', () => {
        if (video.paused) {
            video.play();
            playPauseBtn.querySelector('.icon').textContent = '⏸️';
        } else {
            video.pause();
            playPauseBtn.querySelector('.icon').textContent = '▶️';
        }
    });
    
    // Progress tracking
    video.addEventListener('timeupdate', () => {
        const progress = (video.currentTime / video.duration) * 100;
        progressFill.style.width = progress + '%';
        currentTimeEl.textContent = formatTime(video.currentTime);
    });
    
    video.addEventListener('loadedmetadata', () => {
        durationEl.textContent = formatTime(video.duration);
    });
    
    // Seeking
    progressBar?.addEventListener('click', (e) => {
        const rect = progressBar.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const percentage = clickX / rect.width;
        video.currentTime = percentage * video.duration;
    });
    
    // Volume control
    volumeSlider?.addEventListener('input', (e) => {
        video.volume = e.target.value / 100;
        updateVolumeIcon();
    });
    
    muteBtn?.addEventListener('click', () => {
        video.muted = !video.muted;
        updateVolumeIcon();
    });
    
    function updateVolumeIcon() {
        const icon = muteBtn.querySelector('.icon');
        if (video.muted || video.volume === 0) {
            icon.textContent = '🔇';
        } else if (video.volume < 0.5) {
            icon.textContent = '🔉';
        } else {
            icon.textContent = '🔊';
        }
    }
    
    // Fullscreen
    fullscreenBtn?.addEventListener('click', () => {
        if (video.requestFullscreen) {
            video.requestFullscreen();
        }
    });
}

function initActionButtons() {
    // Like button
    const likeBtn = document.querySelector('.like-btn');
    likeBtn?.addEventListener('click', function() {
        this.classList.toggle('liked');
        // Here you would make an AJAX call to save the like
    });
    
    // Share button
    const shareBtn = document.querySelector('.share-btn');
    shareBtn?.addEventListener('click', function() {
        if (navigator.share) {
            navigator.share({
                title: this.dataset.title,
                url: this.dataset.url
            });
        } else {
            navigator.clipboard.writeText(this.dataset.url);
            alert('¡Enlace copiado al portapapeles!');
        }
    });
}

function initDescriptionToggle() {
    const toggle = document.getElementById('description-toggle');
    const content = document.getElementById('description-content');
    
    if (!toggle || !content) return;
    
    const fullHeight = content.scrollHeight;
    content.style.maxHeight = '100px';
    content.style.overflow = 'hidden';
    
    toggle.addEventListener('click', function() {
        if (content.style.maxHeight === '100px') {
            content.style.maxHeight = fullHeight + 'px';
            this.textContent = 'Ver menos';
        } else {
            content.style.maxHeight = '100px';
            this.textContent = 'Ver más';
        }
    });
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function handleVideoClick(event) {
    // Use the global function from main.js
    if (window.VideoPlayerTheme) {
        window.VideoPlayerTheme.handleVideoClick(event);
    }
}
</script>

<?php get_footer(); ?>