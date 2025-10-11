@extends('layouts.app')

@section('title', 'Contatti - ' . config('app.name'))
@section('description', 'Contattaci per qualsiasi domanda o suggerimento. Siamo qui per aiutarti!')

@section('breadcrumb')
    <x-breadcrumb :items="[['title' => 'Contatti']]" />
@endsection

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-4 fw-bold text-dark mb-3">Contattaci</h1>
                <p class="lead text-muted mb-0">Hai domande? Suggerimenti? Siamo qui per aiutarti!</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Contact Form -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white border-0 py-4">
                    <h2 class="h4 mb-0 text-center">
                        <i class="fas fa-envelope me-2"></i>Invia un Messaggio
                    </h2>
                </div>
                <div class="card-body p-5">
                    <form id="contact-form" action="{{ route('contact.send') }}" method="POST" novalidate>
                        @csrf
                        
                        <!-- Honeypot field (hidden) -->
                        <div style="display: none;">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row">
                            <!-- Name Field -->
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    Nome Completo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Il tuo nome completo"
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="la.tua@email.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject Field -->
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-semibold">
                                Soggetto <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag text-primary"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('subject') is-invalid @enderror" 
                                       id="subject" 
                                       name="subject" 
                                       value="{{ old('subject') }}"
                                       placeholder="Di cosa vuoi parlare?"
                                       required>
                            </div>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                Messaggio <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3">
                                    <i class="fas fa-comment text-primary"></i>
                                </span>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="6"
                                          placeholder="Scrivi qui il tuo messaggio..."
                                          required>{{ old('message') }}</textarea>
                            </div>
                            <div class="form-text">
                                <span id="char-count">0</span> / 2000 caratteri
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" id="submit-btn" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i>Invia Messaggio
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body">
                            <div class="text-primary mb-3">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h5 class="card-title">Email</h5>
                            <p class="text-muted mb-0">
                                <a href="mailto:blog@blog.com" class="text-decoration-none text-muted email-link">
                                    blog@blog.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body">
                            <div class="text-primary mb-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h5 class="card-title">Orari</h5>
                            <p class="text-muted mb-0">Lun-Ven: 9:00-18:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('char-count');
    const submitBtn = document.getElementById('submit-btn');

    // Character counter
    messageField.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count > 2000) {
            charCount.style.color = '#dc3545';
            this.classList.add('is-invalid');
        } else {
            charCount.style.color = '#6c757d';
            this.classList.remove('is-invalid');
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        // Basic validation
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();

        if (!name || !email || !subject || !message) {
            e.preventDefault();
            showError('Compila tutti i campi obbligatori');
            return;
        }

        if (message.length < 10) {
            e.preventDefault();
            showError('Il messaggio deve contenere almeno 10 caratteri');
            return;
        }

        if (message.length > 2000) {
            e.preventDefault();
            showError('Il messaggio non può superare i 2000 caratteri');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showError('Inserisci un indirizzo email valido');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Invio in corso...';
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });
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
    const form = document.getElementById('contact-form');
    if (form) {
        form.insertBefore(alert, form.firstChild);
    }
}
</script>

<style>
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.card {
    border-radius: 1rem;
}

.btn {
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Character counter styling */
#char-count {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1rem;
    }
}

/* Animation for form elements */
.form-control, .btn {
    transition: all 0.3s ease;
}

.form-control:focus {
    transform: translateY(-1px);
}

/* Success animation */
@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.btn-success {
    animation: successPulse 0.6s ease-in-out;
}

/* Email link hover effect */
.email-link:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
}
</style>
@endsection


