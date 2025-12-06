@extends('layouts.app')

@section('title', 'Accedi o Registrati - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Benvenuto
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Scegli come vuoi procedere</p>
                </div>
                <div class="card-body p-5">
                    <div class="row g-4">
                        <!-- Login Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-sign-in-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Hai già un account?</h5>
                                    <p class="card-text text-muted">
                                        Accedi al tuo profilo per gestire i tuoi articoli e preferenze.
                                    </p>
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i>Accedi
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Register Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-user-plus fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Nuovo utente?</h5>
                                    <p class="card-text text-muted">
                                        Registrati per iniziare a scrivere e condividere i tuoi articoli.
                                    </p>
                                    <a href="{{ route('register') }}" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-user-plus me-2"></i>Registrati
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guest Features -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="text-center">
                            <h6 class="text-muted mb-3">Oppure continua come ospite</h6>
                            <p class="text-muted small mb-3">
                                Puoi leggere tutti gli articoli senza registrarti
                            </p>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Torna alla Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

















