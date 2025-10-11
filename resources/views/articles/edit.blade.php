@extends('layouts.app')

@section('title', 'Modifica Articolo - ' . config('app.name'))
@section('description', 'Modifica l\'articolo: ' . $article->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Articoli', 'url' => route('home')],
        ['title' => $article->title, 'url' => route('articles.show', $article)],
        ['title' => 'Modifica']
    ]" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">Modifica Articolo</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Visualizza
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Torna agli Articoli
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-articles.form 
        :article="$article" 
        :action="route('articles.update', $article)" 
        method="PUT" />
</div>
@endsection