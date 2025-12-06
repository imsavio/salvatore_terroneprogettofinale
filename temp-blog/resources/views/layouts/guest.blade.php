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
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
<main class="w-100" style="max-width: 480px;">
    <div class="text-center mb-4">
        <a class="navbar-brand fw-semibold fs-4" href="{{ route('home') }}">NovaBlog</a>
    </div>

    <div class="card card-modern p-4">
        {{ $slot }}
    </div>
</main>

@stack('scripts')
</body>
</html>
