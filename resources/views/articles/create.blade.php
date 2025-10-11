@extends('layouts.app')

@section('title', 'Nuovo Articolo - ' . config('app.name'))
@section('description', 'Crea un nuovo articolo per il blog.')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Articoli', 'url' => route('home')],
        ['title' => 'Nuovo Articolo']
    ]" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">Nuovo Articolo</h1>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Torna agli Articoli
                </a>
            </div>
        </div>
    </div>

    @include('articles.form', [
        'article' => null,
        'action' => route('articles.store'),
        'method' => 'POST'
    ])
</div>
@endsection

