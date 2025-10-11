import './bootstrap';
import 'bootstrap';
import '../scss/app.scss';

// Import TinyMCE (CDN fallback)
// TinyMCE will be loaded via CDN in the layout

// Import Select2 (CDN fallback)
// Select2 will be loaded via CDN in the layout

// Import Dropzone (CDN fallback)
// Dropzone will be loaded via CDN in the layout

// Initialize components when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    if (document.querySelector('#content')) {
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
            branding: false,
            promotion: false
        });
    }

    // Initialize Select2 for tags with autocomplete
    if (document.querySelector('#tags')) {
        $('#tags').select2({
            placeholder: 'Seleziona i tag...',
            allowClear: true,
            width: '100%',
            tags: true,
            tokenSeparators: [','],
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            },
            ajax: {
                url: '/api/tags/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.map(tag => ({
                            id: tag.id,
                            text: tag.name,
                            color: tag.color
                        })),
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
            templateResult: function(tag) {
                if (tag.loading) {
                    return tag.text;
                }
                const $result = $(
                    '<span>' + tag.text + '</span>'
                );
                if (tag.color) {
                    $result.css({
                        'background-color': tag.color + '20',
                        'color': tag.color,
                        'border': '1px solid ' + tag.color + '40',
                        'padding': '2px 6px',
                        'border-radius': '4px',
                        'font-size': '0.875rem'
                    });
                }
                return $result;
            },
            templateSelection: function(tag) {
                if (tag.color) {
                    return $('<span style="background-color: ' + tag.color + '20; color: ' + tag.color + '; border: 1px solid ' + tag.color + '40; padding: 2px 6px; border-radius: 4px; font-size: 0.875rem;">' + tag.text + '</span>');
                }
                return tag.text;
            },
            language: {
                noResults: function() {
                    return "Nessun risultato trovato";
                },
                searching: function() {
                    return "Ricerca in corso...";
                },
                inputTooShort: function() {
                    return "Inserisci almeno 2 caratteri";
                }
            }
        });

        // Handle new tag creation
        $('#tags').on('select2:select', function (e) {
            const data = e.params.data;
            if (data.newTag) {
                // Create new tag via API
                createNewTag(data.text);
            }
        });
    }

    // Initialize Dropzone for image upload
    if (document.querySelector('#image-upload')) {
        Dropzone.autoDiscover = false;
        new Dropzone("#image-upload", {
            url: "/upload-image", // We'll need to create this route
            paramName: "image",
            maxFilesize: 5, // MB
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: "Trascina qui l'immagine o clicca per selezionare",
            dictRemoveFile: "Rimuovi",
            dictCancelUpload: "Annulla",
            dictUploadCanceled: "Upload annullato",
            dictInvalidFileType: "Tipo di file non valido",
            dictFileTooBig: "File troppo grande ({{filesize}}MB). Dimensione massima: {{maxFilesize}}MB",
            init: function() {
                this.on("success", function(file, response) {
                    // Update the hidden input with the image URL
                    document.querySelector('input[name="featured_image"]').value = response.url;
                    // Show preview
                    const preview = document.querySelector('#image-preview');
                    if (preview) {
                        preview.innerHTML = `<img src="${response.url}" class="img-fluid rounded" alt="Preview">`;
                    }
                });
            }
        });
    }

    // Form validation
    const form = document.querySelector('#article-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Basic validation
            const title = document.querySelector('#title');
            const content = tinymce.get('content');
            
            if (!title.value.trim()) {
                e.preventDefault();
                title.classList.add('is-invalid');
                showError('Il titolo è obbligatorio');
                return;
            }
            
            if (content && !content.getContent().trim()) {
                e.preventDefault();
                showError('Il contenuto è obbligatorio');
                return;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvataggio...';
            }
        });
    }
});

function showError(message) {
    // Remove existing alerts
    const existingAlert = document.querySelector('.alert-danger');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the form
    const form = document.querySelector('#article-form');
    if (form) {
        form.insertBefore(alert, form.firstChild);
    }
}

// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('#image-preview');
            if (preview) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" alt="Preview">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Create new tag via API
function createNewTag(tagName) {
    fetch('/api/tags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            name: tagName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            // Update the select2 with the new tag
            const $select = $('#tags');
            const option = new Option(data.name, data.id, true, true);
            option.dataset.color = data.color;
            $select.append(option).trigger('change');
            
            // Show success message
            showSuccess('Tag "' + data.name + '" creato con successo!');
        }
    })
    .catch(error => {
        console.error('Error creating tag:', error);
        showError('Errore nella creazione del tag');
    });
}

// Show success message
function showSuccess(message) {
    // Remove existing alerts
    const existingAlert = document.querySelector('.alert-success');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the form
    const form = document.querySelector('#article-form');
    if (form) {
        form.insertBefore(alert, form.firstChild);
    }
}

// Tag validation
function validateTags() {
    const tags = $('#tags').val();
    if (tags && tags.length > 10) {
        showError('Puoi selezionare al massimo 10 tag');
        return false;
    }
    return true;
}

    // Add tag validation to form submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#article-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateTags()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });

    // ========================================
    // UI/UX ENHANCEMENTS & ANIMATIONS
    // ========================================

    // Page load animations
    document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in animation to main content
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.classList.add('fade-in');
        }

        // Add staggered animation to cards
        const cards = document.querySelectorAll('.article-card, .card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });

        // Add hover effects to buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add loading states to forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Caricamento...';
                    submitBtn.disabled = true;
                }
            });
        });

        // Add smooth scrolling for anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        const elementsToAnimate = document.querySelectorAll('.article-card, .card, .section');
        elementsToAnimate.forEach(el => {
            observer.observe(el);
        });

        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            // ESC key to close modals/alerts
            if (e.key === 'Escape') {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                });
            }
        });

        // Add touch-friendly interactions for mobile
        if ('ontouchstart' in window) {
            const touchElements = document.querySelectorAll('.btn, .card, .nav-link');
            touchElements.forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.classList.add('active');
                });
                
                element.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.classList.remove('active');
                    }, 150);
                });
            });
        }

        // Add focus management for accessibility
        const focusableElements = document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        let currentFocusIndex = 0;

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                
                if (e.shiftKey) {
                    currentFocusIndex = currentFocusIndex > 0 ? currentFocusIndex - 1 : focusableElements.length - 1;
                } else {
                    currentFocusIndex = currentFocusIndex < focusableElements.length - 1 ? currentFocusIndex + 1 : 0;
                }
                
                focusableElements[currentFocusIndex].focus();
            }
        });

        // Add ARIA labels for better accessibility
        const images = document.querySelectorAll('img:not([alt])');
        images.forEach(img => {
            img.setAttribute('alt', 'Immagine');
        });

        // Add skip links for screen readers
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.textContent = 'Salta al contenuto principale';
        skipLink.className = 'skip-link';
        skipLink.style.cssText = `
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-600);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            transition: top 0.3s;
        `;
        
        skipLink.addEventListener('focus', function() {
            this.style.top = '6px';
        });
        
        skipLink.addEventListener('blur', function() {
            this.style.top = '-40px';
        });
        
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Add main content ID for skip link
        const mainElement = document.querySelector('main');
        if (mainElement) {
            mainElement.id = 'main-content';
        }
    });

    // ========================================
    // PERFORMANCE OPTIMIZATIONS
    // ========================================

    // Debounce function for search inputs
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Add debounced search for better performance
    const searchInputs = document.querySelectorAll('input[type="search"], input[name="search"]');
    searchInputs.forEach(input => {
        const debouncedSearch = debounce(function() {
            // Trigger search functionality
            console.log('Searching for:', this.value);
        }, 300);
        
        input.addEventListener('input', debouncedSearch);
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ========================================
    // ERROR HANDLING & USER FEEDBACK
    // ========================================

    // Global error handler
    window.addEventListener('error', function(e) {
        console.error('Global error:', e.error);
        showNotification('Si è verificato un errore. Riprova più tardi.', 'error');
    });

    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: var(--shadow-lg);
        `;
        
        notification.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Add loading overlay for AJAX requests
    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;
        
        overlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
                <p class="mt-3">Caricamento...</p>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }

    function hideLoadingOverlay() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Export functions for global use
    window.showNotification = showNotification;
    window.showLoadingOverlay = showLoadingOverlay;
    window.hideLoadingOverlay = hideLoadingOverlay;
