<x-guest-layout>
    <h1 class="fw-semibold mb-3">Recupero password</h1>
    <p class="text-muted mb-4">Inserisci il tuo indirizzo email: ti invieremo un link per impostare una nuova password.</p>

    @if (session('status'))
        <div class="alert alert-modern alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-cta">Invia link di reset</button>
    </form>

    <p class="text-center text-muted mt-4 mb-0">
        Ricordi la password? <a href="{{ route('login') }}">Torna al login</a>.
    </p>
</x-guest-layout>
