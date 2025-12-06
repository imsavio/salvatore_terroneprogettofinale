<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NovaBlog') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark py-3 fixed-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('home') }}">NovaBlog</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.index') ? 'active' : '' }}" href="{{ route('articles.index') }}">Articoli</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact.show') ? 'active' : '' }}" href="{{ route('contact.show') }}">Contatti</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.create') ? 'active' : '' }}" href="{{ route('articles.create') }}">Nuovo articolo</a></li>
                @endauth
            </ul>

            <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0">
                @auth
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
                    <li class="nav-item"><a class="btn btn-outline-light ms-lg-2" href="{{ route('register') }}">Registrati</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
    <div class="pt-5"></div>

    @if(session('status'))
        <div class="container pt-4">
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
            <div class="col-md-6">
                <h5 class="text-white mb-3">NovaBlog</h5>
                <p class="mb-0">Un ambiente moderno per condividere idee e racconti con stile.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a class="me-3" href="{{ route('articles.index') }}">Articoli</a>
                <a class="me-3" href="{{ route('contact.show') }}">Contatti</a>
                <a href="mailto:{{ config('mail.from.address') }}">Supporto</a>
            </div>
        </div>
        <div class="pt-4 d-flex justify-content-between border-top border-secondary mt-4">
            <small>© {{ now()->year }} NovaBlog. Tutti i diritti riservati.</small>
            <small>Made with Laravel & Bootstrap</small>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
