@extends('layouts.app')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Accedi
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Indirizzo Email
                            </label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email" autofocus
                                   placeholder="Inserisci la tua email">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password"
                                   placeholder="Inserisci la tua password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" 
                                   id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Ricordami
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Accedi
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>Password dimenticata?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">
                        Non hai un account? 
                        <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                            <i class="fas fa-user-plus me-1"></i>Registrati qui
                        </a>
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('auth') }}" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Torna alla scelta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection