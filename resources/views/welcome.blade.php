@extends('layouts.app')

@section('title', 'Welcome - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">Welcome to {{ config('app.name', 'Laravel Blog') }}</h1>
                <p class="lead text-muted">A modern blog platform built with Laravel 10 and Bootstrap 5</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-rocket fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Fast & Modern</h5>
                    <p class="card-text">Built with the latest Laravel 10 framework and Bootstrap 5 for optimal performance.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Secure Authentication</h5>
                    <p class="card-text">Powered by Laravel Fortify with registration, login, and password reset features.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Responsive Design</h5>
                    <p class="card-text">Fully responsive design that works perfectly on all devices and screen sizes.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            @guest
                <h3 class="mb-4">Ready to get started?</h3>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>
                        Create Account
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </a>
                </div>
            @else
                <h3 class="mb-4">Welcome back, {{ Auth::user()->name }}!</h3>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Go to Dashboard
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection