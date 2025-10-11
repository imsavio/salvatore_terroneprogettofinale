<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'Laravel Blog'))</title>
    <meta name="description" content="@yield('description', 'A modern blog built with Laravel 10 and Bootstrap 5. Read the latest articles about web development, programming, and technology.')">
    <meta name="keywords" content="@yield('keywords', 'laravel, php, web development, programming, blog, bootstrap')">
    <meta name="author" content="{{ config('app.name', 'Laravel Blog') }}">
    <meta name="robots" content="index, follow">
    <meta name="language" content="it">
    <meta name="revisit-after" content="7 days">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('title', config('app.name', 'Laravel Blog'))">
    <meta property="og:description" content="@yield('description', 'A modern blog built with Laravel 10 and Bootstrap 5.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Laravel Blog') }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="it_IT">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name', 'Laravel Blog'))">
    <meta name="twitter:description" content="@yield('description', 'A modern blog built with Laravel 10 and Bootstrap 5.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta name="twitter:site" content="@yield('twitter_site', '@laravelblog')">
    <meta name="twitter:creator" content="@yield('twitter_creator', '@laravelblog')">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#3b82f6">
    <meta name="msapplication-TileColor" content="#3b82f6">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- Structured Data -->
    @yield('structured_data')
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Dropzone -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    
    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    
    <!-- Critical CSS for above-the-fold content -->
    <style>
        /* Critical CSS for immediate rendering */
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .navbar { 
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 1rem; 
        }
        @media (max-width: 768px) {
            .container { padding: 0 0.5rem; }
        }
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary" role="navigation" aria-label="Navigazione principale">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}" aria-label="Torna alla homepage">
                    <i class="fas fa-blog me-2" aria-hidden="true"></i>
                    {{ config('app.name', 'Laravel Blog') }}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Apri menu di navigazione">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto" role="menubar">
                        <li class="nav-item" role="none">
                            <a class="nav-link" href="{{ route('home') }}" role="menuitem" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
                                <i class="fas fa-home me-1" aria-hidden="true"></i>Home
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav" role="menubar">
                        @guest
                            <li class="nav-item" role="none">
                                <a class="nav-link" href="{{ route('auth') }}" role="menuitem">
                                    <i class="fas fa-user-circle me-1" aria-hidden="true"></i>Accedi
                                </a>
                            </li>
                            <li class="nav-item" role="none">
                                <a class="nav-link" href="{{ route('contact') }}" role="menuitem" aria-current="{{ request()->routeIs('contact*') ? 'page' : 'false' }}">
                                    <i class="fas fa-envelope me-1" aria-hidden="true"></i>Contatti
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown" role="none">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" 
                                   aria-expanded="false" aria-haspopup="true" id="userMenu">
                                    <i class="fas fa-user me-1" aria-hidden="true"></i>
                                    <span class="visually-hidden">Menu utente per</span>{{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" role="menu" aria-labelledby="userMenu">
                                    <li role="none">
                                        <a class="dropdown-item" href="{{ route('profile') }}" role="menuitem" aria-current="{{ request()->routeIs('profile*') ? 'page' : 'false' }}">
                                            <i class="fas fa-user me-2" aria-hidden="true"></i>Il Mio Profilo
                                        </a>
                                    </li>
                                    <li role="none">
                                        <a class="dropdown-item" href="{{ route('articles.create') }}" role="menuitem">
                                            <i class="fas fa-plus me-2" aria-hidden="true"></i>Nuovo Articolo
                                        </a>
                                    </li>
                                    <li role="none">
                                        <a class="dropdown-item" href="{{ route('contact') }}" role="menuitem" aria-current="{{ request()->routeIs('contact*') ? 'page' : 'false' }}">
                                            <i class="fas fa-envelope me-2" aria-hidden="true"></i>Contatti
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" role="separator"></li>
                                    <li role="none">
                                        <a class="dropdown-item" href="{{ route('logout') }}" role="menuitem"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show m-0" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Breadcrumb -->
        @hasSection('breadcrumb')
            <div class="bg-light border-bottom">
                <div class="container py-2">
                    @yield('breadcrumb')
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="py-4" id="main-content" role="main" aria-label="Contenuto principale">
            @yield('content')
        </main>

        <!-- Tag Cloud Sidebar (if not in tag pages) -->
        @if(!request()->routeIs('tags.*'))
            @php
                $popularTags = \App\Models\Tag::withCount('articles')
                    ->whereHas('articles')
                    ->orderBy('articles_count', 'desc')
                    ->limit(8)
                    ->get();
            @endphp
            
            @if($popularTags->count() > 0)
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tags me-2 text-primary"></i>Tag Popolari
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($popularTags as $tag)
                                            <a href="{{ route('tags.show', $tag) }}" 
                                               class="text-decoration-none">
                                                <span class="badge fs-6 px-2 py-1" 
                                                      style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                                    {{ $tag->name }} ({{ $tag->articles_count }})
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-center">
                                        <a href="{{ route('tags.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-th-large me-1"></i>Vedi Tutti i Tag
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Footer -->
        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>{{ config('app.name', 'Laravel Blog') }}</h5>
                        <p class="mb-0">A modern blog built with Laravel 10 and Bootstrap 5.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel Blog') }}. 
                            All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
