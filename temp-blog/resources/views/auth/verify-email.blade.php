<x-guest-layout>
    <h1 class="fw-semibold mb-3">Verifica la tua email</h1>
    <p class="text-muted mb-4">Grazie per esserti registrato! Controlla la tua casella di posta e clicca sul link di verifica per attivare l'account.</p>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-modern alert-success">
            Abbiamo inviato un nuovo link di verifica all'indirizzo email indicato durante la registrazione.
        </div>
    @endif

    <div class="d-flex gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Invia di nuovo il link</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Esci</button>
        </form>
    </div>
</x-guest-layout>
