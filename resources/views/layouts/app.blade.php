<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VITAH') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <span class="brand-text">VITAH</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.index') ? 'active' : '' }}" href="{{ route('articles.index') }}">Storie</a></li>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.create') ? 'active' : '' }}" href="{{ route('articles.create') }}">Raccontati</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('friends.*') ? 'active' : '' }}" href="{{ route('friends.index') }}">Amici</a></li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                                Messaggi
                                @if(auth()->user()->unreadMessagesCount() > 0)
                                    <span class="badge bg-danger ms-1">{{ auth()->user()->unreadMessagesCount() }}</span>
                                @endif
                            </a>
                        </li>
                    @endauth
                </ul>

            <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">Notifiche</h6>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-primary">Segna tutte come lette</button>
                                    </form>
                                @endif
                            </li>
                            @if(auth()->user()->notifications->count() > 0)
                                @foreach(auth()->user()->notifications->take(5) as $notification)
                                    <li>
                                        <a class="dropdown-item {{ $notification->read_at ? '' : 'bg-light' }}" href="{{ route('notifications.index') }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                                    <i class="bi bi-person-plus"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small {{ $notification->read_at ? 'text-muted' : 'fw-semibold' }}">{{ $notification->data['message'] ?? 'Nuova notifica' }}</p>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-primary rounded-pill flex-shrink-0" style="width: 8px; height: 8px; padding: 0;"></span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center text-primary" href="{{ route('notifications.index') }}">Vedi tutte le notifiche</a></li>
                            @else
                                <li class="px-3 py-4 text-center text-muted">
                                    <small>Nessuna notifica</small>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profilo</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">Esci</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Accedi</a></li>
                    <li class="nav-item"><a class="btn btn-primary ms-lg-2" href="{{ route('register') }}">Registrati</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
    <div class="pt-5"></div>

    @if(session('status'))
        <div class="container pt-5 pb-3">
            <div class="alert alert-modern alert-success d-flex align-items-center gap-2" role="alert">
                <span class="fw-semibold">{{ session('status') }}</span>
            </div>
        </div>
    @endif

    {{ $slot }}
</main>

<footer class="footer mt-5 py-5">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-12">
                <h5 class="mb-3">VITAH</h5>
                <p class="mb-1">Condividi la tua storia in modo anonimo o con il tuo nome. Ogni vita merita di essere raccontata.</p>
                <a href="{{ route('contact.show') }}" class="text-decoration-none" style="color: #0d6efd;">Contatti</a>
            </div>
        </div>
        <div class="pt-4 d-flex justify-content-between border-top mt-4" style="border-color: rgba(0, 0, 0, 0.1) !important;">
                <small style="color: #6e6e73;">© {{ now()->year }} VITAH. Tutti i diritti riservati.</small>
        </div>
    </div>
</footer>

@stack('scripts')

<x-cookie-banner />

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookieBanner');
    const cookieAccept = document.getElementById('cookieAccept');
    const cookieReject = document.getElementById('cookieReject');

    // Ritorna il valore del cookie se esiste, altrimenti null
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    if (cookieBanner) {
        const existingConsent = getCookie('cookie_consent');

        // Se l'utente ha già espresso una scelta, non mostrare più il banner
        if (existingConsent === 'accepted' || existingConsent === 'rejected') {
            cookieBanner.style.display = 'none';
            return;
        }

        // Mostra il banner dopo un breve delay solo se non c'è ancora consenso
        setTimeout(() => {
            cookieBanner.style.display = 'block';
        }, 500);

        // Gestione accettazione
        if (cookieAccept) {
            cookieAccept.addEventListener('click', function() {
                setCookieConsent('accepted');
                hideBanner();
            });
        }

        // Gestione rifiuto
        if (cookieReject) {
            cookieReject.addEventListener('click', function() {
                setCookieConsent('rejected');
                hideBanner();
            });
        }

        function setCookieConsent(status) {
            // Imposta il cookie con scadenza di 1 anno
            const expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);

            document.cookie = `cookie_consent=${status}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
        }

        function hideBanner() {
            if (cookieBanner) {
                cookieBanner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                cookieBanner.style.opacity = '0';
                cookieBanner.style.transform = 'translateY(100%)';

                setTimeout(() => {
                    cookieBanner.style.display = 'none';
                }, 300);
            }
        }
    }
});
</script>
</body>
</html>
