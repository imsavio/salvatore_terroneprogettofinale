<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-modern alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="fw-semibold mb-3">Bentornato</h1>
    <p class="text-muted mb-4">Accedi per gestire i tuoi articoli e continuare la pubblicazione.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember_me">Ricordami</label>
            </div>

            @if (Route::has('password.request'))
                <a class="text-decoration-none" href="{{ route('password.request') }}">Password dimenticata?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-cta">Accedi</button>
    </form>

    <p class="text-center text-muted mt-4 mb-0">
        Non hai un account? <a href="{{ route('register') }}">Registrati ora</a>.
    </p>
</x-guest-layout>
