<?php
/**
 * Search form template
 * 
 * @package VideoPlayerMobile
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="search-form-container">
        <label for="search-field-<?php echo esc_attr(uniqid()); ?>" class="sr-only">
            <?php esc_html_e('Buscar videos...', 'videoplayer'); ?>
        </label>
        
        <input 
            type="search" 
            id="search-field-<?php echo esc_attr(uniqid()); ?>"
            class="search-field" 
            placeholder="<?php esc_attr_e('Buscar videos...', 'videoplayer'); ?>" 
            value="<?php echo get_search_query(); ?>" 
            name="s"
            autocomplete="off"
            required
        />
        
        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Buscar', 'videoplayer'); ?>">
            <span class="search-icon">🔍</span>
            <span class="search-text"><?php esc_html_e('Buscar', 'videoplayer'); ?></span>
        </button>
        
        <!-- Hidden field to search only videos -->
        <input type="hidden" name="post_type" value="video">
    </div>
    
    <!-- Search suggestions -->
    <div class="search-suggestions" id="search-suggestions" style="display: none;">
        <div class="suggestions-header">
            <?php esc_html_e('Sugerencias:', 'videoplayer'); ?>
        </div>
        <ul class="suggestions-list" id="suggestions-list"></ul>
    </div>
</form>

<style>
.search-form {
    position: relative;
    width: 100%;
    max-width: 500px;
}

.search-form-container {
    display: flex;
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
}

.search-form-container:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

.search-field {
    flex: 1;
    background: transparent;
    border: none;
    padding: 12px 15px;
    color: var(--light-text);
    font-size: 14px;
    outline: none;
}

.search-field::placeholder {
    color: var(--muted-text);
}

.search-submit {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 12px 20px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
}

.search-submit:hover {
    background: linear-gradient(45deg, #ff5555, #3eccc4);
    transform: translateY(-1px);
}

.search-icon {
    font-size: 16px;
}

.search-text {
    display: none;
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    z-index: 100;
    max-height: 300px;
    overflow-y: auto;
}

.suggestions-header {
    padding: 10px 15px;
    font-size: 12px;
    color: var(--muted-text);
    border-bottom: 1px solid var(--border-color);
    font-weight: 600;
}

.suggestions-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.suggestion-item {
    padding: 12px 15px;
    cursor: pointer;
    transition: var(--transition);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    gap: 10px;
}

.suggestion-item:hover,
.suggestion-item.active {
    background: var(--hover-bg);
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-icon {
    font-size: 14px;
    opacity: 0.7;
}

.suggestion-text {
    flex: 1;
    font-size: 14px;
}

.suggestion-meta {
    font-size: 12px;
    color: var(--muted-text);
}

/* Recent searches */
.recent-searches {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.recent-searches-title {
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 10px;
    font-weight: 600;
}

.recent-search-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.recent-search-tag {
    background: rgba(255, 255, 255, 0.1);
    color: var(--muted-text);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
}

.recent-search-tag:hover {
    background: var(--primary-color);
    color: white;
}

/* Popular searches */
.popular-searches {
    padding: 15px;
}

.popular-searches-title {
    font-size: 12px;
    color: var(--muted-text);
    margin-bottom: 10px;
    font-weight: 600;
}

.popular-search-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.popular-search-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.popular-search-item:last-child {
    border-bottom: none;
}

.popular-search-link {
    color: var(--muted-text);
    text-decoration: none;
    font-size: 13px;
    transition: var(--transition);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popular-search-link:hover {
    color: var(--primary-color);
}

.popular-search-count {
    font-size: 11px;
    opacity: 0.7;
}

/* Responsive */
@media (min-width: 768px) {
    .search-text {
        display: inline;
    }
    
    .search-submit {
        padding: 12px 25px;
    }
}

@media (max-width: 480px) {
    .search-submit {
        padding: 12px 15px;
    }
    
    .search-icon {
        font-size: 14px;
    }
}

/* Loading state */
.search-form.loading .search-submit {
    background: var(--muted-text);
    cursor: not-allowed;
}

.search-form.loading .search-icon {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Voice search (if supported) */
.voice-search-btn {
    background: none;
    border: none;
    color: var(--muted-text);
    padding: 8px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 16px;
}

.voice-search-btn:hover {
    color: var(--primary-color);
}

.voice-search-btn.listening {
    color: var(--primary-color);
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForms = document.querySelectorAll('.search-form');
    
    searchForms.forEach(form => {
        const searchField = form.querySelector('.search-field');
        const suggestions = form.querySelector('.search-suggestions');
        const suggestionsList = form.querySelector('.suggestions-list');
        let currentSuggestionIndex = -1;
        let searchTimeout;
        
        // Search input handler
        if (searchField && suggestions) {
            searchField.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        fetchSuggestions(query, suggestionsList, suggestions);
                    }, 300);
                } else {
                    suggestions.style.display = 'none';
                    showDefaultSuggestions(suggestionsList, suggestions);
                }
            });
            
            // Keyboard navigation
            searchField.addEventListener('keydown', function(e) {
                const suggestionItems = suggestions.querySelectorAll('.suggestion-item');
                
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, suggestionItems.length - 1);
                        updateSuggestionSelection(suggestionItems);
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                        updateSuggestionSelection(suggestionItems);
                        break;
                        
                    case 'Enter':
                        if (currentSuggestionIndex >= 0 && suggestionItems[currentSuggestionIndex]) {
                            e.preventDefault();
                            suggestionItems[currentSuggestionIndex].click();
                        }
                        break;
                        
                    case 'Escape':
                        suggestions.style.display = 'none';
                        currentSuggestionIndex = -1;
                        break;
                }
            });
            
            // Show suggestions on focus
            searchField.addEventListener('focus', function() {
                if (this.value.trim().length >= 2) {
                    suggestions.style.display = 'block';
                } else {
                    showDefaultSuggestions(suggestionsList, suggestions);
                }
            });
            
            // Hide suggestions on outside click
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) {
                    suggestions.style.display = 'none';
                    currentSuggestionIndex = -1;
                }
            });
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const query = searchField.value.trim();
            if (query) {
                // Save to recent searches
                saveRecentSearch(query);
                
                // Add loading state
                form.classList.add('loading');
            }
        });
    });
    
    // Fetch search suggestions
    function fetchSuggestions(query, container, suggestionsDiv) {
        // Simulate API call - replace with actual AJAX call to WordPress
        const mockSuggestions = [
            { text: query + ' tutorial', type: 'search', meta: '150 videos' },
            { text: query + ' básico', type: 'search', meta: '89 videos' },
            { text: query + ' avanzado', type: 'search', meta: '67 videos' },
            { text: query + ' 2024', type: 'search', meta: '234 videos' },
            { text: query + ' español', type: 'search', meta: '45 videos' }
        ];
        
        displaySuggestions(mockSuggestions, container, suggestionsDiv);
    }
    
    // Display suggestions
    function displaySuggestions(suggestions, container, suggestionsDiv) {
        container.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const li = document.createElement('li');
            li.className = 'suggestion-item';
            li.innerHTML = `
                <span class="suggestion-icon">${suggestion.type === 'video' ? '📹' : '🔍'}</span>
                <span class="suggestion-text">${suggestion.text}</span>
                <span class="suggestion-meta">${suggestion.meta}</span>
            `;
            
            li.addEventListener('click', function() {
                const searchField = suggestionsDiv.closest('.search-form').querySelector('.search-field');
                searchField.value = suggestion.text;
                suggestionsDiv.style.display = 'none';
                searchField.closest('form').submit();
            });
            
            container.appendChild(li);
        });
        
        suggestionsDiv.style.display = 'block';
    }
    
    // Show default suggestions (recent and popular searches)
    function showDefaultSuggestions(container, suggestionsDiv) {
        const recentSearches = getRecentSearches();
        const popularSearches = ['tutorial wordpress', 'diseño web', 'javascript', 'css grid', 'responsive design'];
        
        let html = '';
        
        if (recentSearches.length > 0) {
            html += `
                <div class="recent-searches">
                    <div class="recent-searches-title">Búsquedas recientes</div>
                    <div class="recent-search-tags">
                        ${recentSearches.map(search => 
                            `<a href="?s=${encodeURIComponent(search)}&post_type=video" class="recent-search-tag">${search}</a>`
                        ).join('')}
                    </div>
                </div>
            `;
        }
        
        html += `
            <div class="popular-searches">
                <div class="popular-searches-title">Búsquedas populares</div>
                <ul class="popular-search-list">
                    ${popularSearches.map(search => 
                        `<li class="popular-search-item">
                            <a href="?s=${encodeURIComponent(search)}&post_type=video" class="popular-search-link">
                                <span>${search}</span>
                                <span class="popular-search-count">${Math.floor(Math.random() * 100) + 10}</span>
                            </a>
                        </li>`
                    ).join('')}
                </ul>
            </div>
        `;
        
        container.innerHTML = html;
        suggestionsDiv.style.display = 'block';
    }
    
    // Update suggestion selection
    function updateSuggestionSelection(items) {
        items.forEach((item, index) => {
            item.classList.toggle('active', index === currentSuggestionIndex);
        });
    }
    
    // Save recent search
    function saveRecentSearch(query) {
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        
        // Remove if already exists
        recentSearches = recentSearches.filter(search => search !== query);
        
        // Add to beginning
        recentSearches.unshift(query);
        
        // Keep only last 5
        recentSearches = recentSearches.slice(0, 5);
        
        localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
    }
    
    // Get recent searches
    function getRecentSearches() {
        return JSON.parse(localStorage.getItem('recentSearches') || '[]');
    }
    
    // Voice search (if supported)
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        addVoiceSearchSupport();
    }
    
    function addVoiceSearchSupport() {
        const searchForms = document.querySelectorAll('.search-form');
        
        searchForms.forEach(form => {
            const searchField = form.querySelector('.search-field');
            const container = form.querySelector('.search-form-container');
            
            // Add voice search button
            const voiceBtn = document.createElement('button');
            voiceBtn.type = 'button';
            voiceBtn.className = 'voice-search-btn';
            voiceBtn.innerHTML = '🎤';
            voiceBtn.setAttribute('aria-label', 'Búsqueda por voz');
            
            container.insertBefore(voiceBtn, container.lastElementChild);
            
            // Voice recognition
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'es-ES';
            
            voiceBtn.addEventListener('click', function() {
                if (this.classList.contains('listening')) {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });
            
            recognition.onstart = function() {
                voiceBtn.classList.add('listening');
                voiceBtn.innerHTML = '🎙️';
            };
            
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                searchField.value = transcript;
                searchField.focus();
                searchField.dispatchEvent(new Event('input'));
            };
            
            recognition.onend = function() {
                voiceBtn.classList.remove('listening');
                voiceBtn.innerHTML = '🎤';
            };
            
            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                voiceBtn.classList.remove('listening');
                voiceBtn.innerHTML = '🎤';
            };
        });
    }
});
</script>