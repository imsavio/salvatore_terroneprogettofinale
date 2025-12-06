<x-app-layout>
    <section class="container py-5">
        <div class="mb-4">
            <span class="badge badge-soft">Impostazioni account</span>
            <h1 class="fw-semibold mt-3 mb-1">Gestisci il tuo profilo</h1>
            <p class="text-muted">Aggiorna le informazioni personali, modifica la password o disattiva l'account.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-modern p-4 h-100">
                    <h2 class="h4 fw-semibold mb-3">Informazioni di profilo</h2>
                    <p class="text-muted small mb-4">Assicurati che le tue informazioni siano aggiornate così possiamo contattarti al bisogno.</p>

                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-modern alert-success">Profilo aggiornato con successo.</div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-primary">Salva modifiche</button>
                    </form>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <form method="post" action="{{ route('verification.send') }}" class="mt-3">
                            @csrf
                            <button class="btn btn-link p-0">Invia di nuovo l'email di verifica</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-modern p-4 mb-4">
                    <h2 class="h4 fw-semibold mb-3">Aggiorna password</h2>
                    <p class="text-muted small mb-4">Scegli una password sicura per proteggere il tuo account.</p>

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-modern alert-success">Password aggiornata con successo.</div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password attuale</label>
                            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nuova password</label>
                            <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Conferma nuova password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" required autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-outline-primary">Aggiorna password</button>
                    </form>
                </div>

                <div class="card card-modern p-4">
                    <h2 class="h5 fw-semibold text-danger mb-3">Disattiva account</h2>
                    <p class="text-muted small">Eliminando il tuo account verranno rimossi definitivamente tutti i contenuti associati.</p>

                    <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Sei sicuro di voler eliminare definitivamente il tuo account?');">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Conferma con la password</label>
                            <input id="delete_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required autocomplete="current-password">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-danger">Elimina account</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
