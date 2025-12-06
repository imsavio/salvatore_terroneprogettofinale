<header class="sticky-top" role="banner">
    <nav class="navbar navbar-expand-lg" aria-label="Navigazione principale">
        <div class="container content-container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <span class="fw-semibold">{{ config('app.name', 'Laravel Blog') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Apri menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <button id="themeToggle" class="btn btn-ghost btn-sm" type="button" aria-label="Cambia tema" title="Cambia tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    @guest
                        <a class="btn btn-dark btn-sm" href="{{ route('auth') }}">Accedi</a>
                        <a class="btn btn-ghost btn-sm" href="{{ route('contact') }}">Contatti</a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-ghost btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">Profilo</a></li>
                                <li><a class="dropdown-item" href="{{ route('articles.create') }}">Nuovo Articolo</a></li>
                                <li><a class="dropdown-item" href="{{ route('contact') }}">Contatti</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</header>











