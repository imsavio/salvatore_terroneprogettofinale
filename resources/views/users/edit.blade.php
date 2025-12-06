@extends('layouts.app')

@section('title', 'Modifica Profilo - ' . config('app.name'))
@section('description', 'Aggiorna le informazioni del tuo profilo.')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Il Mio Profilo', 'url' => route('profile')],
        ['title' => 'Modifica']
    ]" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-user-edit me-2 text-primary"></i>Modifica Profilo
                    </h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Avatar</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="text-center">
                                        <img id="avatar-preview" 
                                             src="{{ $user->avatar_url }}" 
                                             alt="Avatar Preview" 
                                             class="rounded-circle border" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="mt-2">
                                            <label for="avatar" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-camera me-1"></i>Scegli Immagine
                                            </label>
                                            <input type="file" 
                                                   class="form-control d-none" 
                                                   id="avatar" 
                                                   name="avatar" 
                                                   accept="image/*"
                                                   onchange="previewAvatar(this)">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Formati supportati: JPEG, PNG, JPG, GIF. Dimensione massima: 2MB
                                        </div>
                                        @error('avatar')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nome Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-semibold">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}" 
                                       required>
                                <div class="form-text">Solo lettere, numeri, trattini e underscore</div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="bio" class="form-label fw-semibold">Biografia</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" 
                                          name="bio" 
                                          rows="4"
                                          placeholder="Racconta qualcosa di te...">{{ old('bio', $user->bio) }}</textarea>
                                <div class="form-text">Massimo 500 caratteri</div>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-header bg-transparent border-0 py-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-lock me-2 text-warning"></i>Cambia Password
                                </h5>
                                <small class="text-muted">Lascia vuoto se non vuoi cambiare la password</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="current_password" class="form-label fw-semibold">
                                            Password Attuale
                                        </label>
                                        <input type="password" 
                                               class="form-control @error('current_password') is-invalid @enderror" 
                                               id="current_password" 
                                               name="current_password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label fw-semibold">
                                            Nuova Password
                                        </label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label fw-semibold">
                                            Conferma Nuova Password
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salva Modifiche
                            </button>
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annulla
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');
    const password = document.getElementById('password');
    const currentPassword = document.getElementById('current_password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    form.addEventListener('submit', function(e) {
        // Check if password is being changed
        if (password.value) {
            if (!currentPassword.value) {
                e.preventDefault();
                currentPassword.classList.add('is-invalid');
                showError('Inserisci la password attuale per cambiarla');
                return;
            }
            
            if (password.value !== passwordConfirmation.value) {
                e.preventDefault();
                passwordConfirmation.classList.add('is-invalid');
                showError('Le password non coincidono');
                return;
            }
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvataggio...';
        }
    });

    // Real-time password confirmation validation
    passwordConfirmation.addEventListener('input', function() {
        if (password.value && this.value && password.value !== this.value) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
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
    const form = document.getElementById('profile-form');
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

.card {
    border-radius: 0.75rem;
}

.btn {
    border-radius: 0.5rem;
}

/* Avatar preview styling */
#avatar-preview {
    border: 3px solid #e9ecef;
    transition: border-color 0.2s ease;
}

#avatar-preview:hover {
    border-color: #0d6efd;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.gap-4 {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection



















