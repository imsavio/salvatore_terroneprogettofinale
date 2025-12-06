<x-guest-layout>
    <h1 class="fw-semibold mb-3">Crea un account</h1>
    <p class="text-muted mb-4">Registra un profilo per iniziare a pubblicare i tuoi articoli.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome e cognome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimo 8 caratteri, meglio se con numeri e simboli.</small>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Conferma password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-cta">Registrati</button>
    </form>

    <p class="text-center text-muted mt-4 mb-0">
        Hai già un account? <a href="{{ route('login') }}">Accedi</a>.
    </p>
</x-guest-layout>
