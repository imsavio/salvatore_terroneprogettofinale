@extends('layouts.app')

@section('title', 'Welcome - ' . config('app.name'))

@section('content')
<section class="py-5 py-lg-6 text-center">
    <div class="mx-auto" style="max-width: 820px;">
        <small class="text-uppercase text-muted">Benvenuto</small>
        <h1 class="display-4 fw-semibold mt-2">{{ config('app.name', 'Laravel Blog') }}</h1>
        <p class="fs-5 text-muted mb-0">Un blog essenziale, veloce e moderno. Focus su qualità e semplicità.</p>
    </div>
</section>

<section class="py-4">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4 col-lg-3">
            <div class="border rounded-3 p-4 h-100 text-center">
                <span class="text-uppercase small text-muted">Performance</span>
                <h5 class="mt-2 mb-2">Veloce e leggero</h5>
                <p class="text-muted mb-0">Architettura pulita e asset ottimizzati per caricamenti rapidi.</p>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="border rounded-3 p-4 h-100 text-center">
                <span class="text-uppercase small text-muted">Sicurezza</span>
                <h5 class="mt-2 mb-2">Autenticazione solida</h5>
                <p class="text-muted mb-0">Basata su Laravel Breeze con best practice aggiornate.</p>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="border rounded-3 p-4 h-100 text-center">
                <span class="text-uppercase small text-muted">Responsive</span>
                <h5 class="mt-2 mb-2">Design adattivo</h5>
                <p class="text-muted mb-0">Esperienza coerente su desktop, tablet e smartphone.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-center">
    @guest
        <h3 class="mb-3">Inizia ora</h3>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-dark btn-lg">Crea account</a>
            <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">Accedi</a>
        </div>
    @else
        <h3 class="mb-3">Bentornato, {{ Auth::user()->name }}!</h3>
        <a href="{{ route('home') }}" class="btn btn-dark btn-lg">Vai alla Dashboard</a>
    @endguest
</section>
@endsection