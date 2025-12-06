<x-guest-layout>
    <h1 class="fw-semibold mb-3">Conferma password</h1>
    <p class="text-muted mb-4">Per accedere a quest'area protetta inserisci nuovamente la tua password.</p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-cta">Conferma</button>
    </form>
</x-guest-layout>
