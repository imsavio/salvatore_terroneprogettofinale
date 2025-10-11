@extends('layouts.app')

@section('title', 'Articoli - ' . config('app.name'))
@section('description', 'Scopri tutti gli articoli del nostro blog. Leggi i contenuti più recenti su Laravel, PHP, JavaScript e molto altro.')

@section('breadcrumb')
    <x-breadcrumb :items="[['title' => 'Articoli']]" />
@endsection

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h2 mb-1">Articoli</h1>
                    <p class="text-muted mb-0">Scopri i nostri contenuti più recenti</p>
                </div>
                @auth
                    <a href="{{ route('articles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nuovo Articolo
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('articles.index') }}" class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cerca</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Cerca per titolo, contenuto...">
                            </div>
                        </div>

                        <!-- Tag Filter -->
                        <div class="col-md-2">
                            <label for="tag" class="form-label">Tag</label>
                            <select class="form-select" id="tag" name="tag">
                                <option value="">Tutti i tag</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->slug }}" {{ request('tag') == $tag->slug ? 'selected' : '' }}>
                                        {{ $tag->name }} ({{ $tag->articles_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Author Filter -->
                        <div class="col-md-2">
                            <label for="author" class="form-label">Autore</label>
                            <select class="form-select" id="author" name="author">
                                <option value="">Tutti gli autori</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }} ({{ $author->articles_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Da</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">A</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>

                        <!-- Sort -->
                        <div class="col-md-3">
                            <label for="sort" class="form-label">Ordina per</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Più recenti</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Più vecchi</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Titolo A-Z</option>
                                <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Autore</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-9 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filtra
                            </button>
                            <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    @if(request()->hasAny(['search', 'tag', 'author', 'date_from', 'date_to']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ $articles->total() }}</strong> articoli trovati
                    @if(request('search'))
                        per "<strong>{{ request('search') }}</strong>"
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Articles Grid -->
    <div class="row g-4">
        @forelse ($articles as $article)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card article-card h-100 border-0 shadow-sm position-relative">
                    <!-- Action buttons for owners -->
                    @auth
                        @can('update', $article)
                            <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('articles.edit', $article) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Sei sicuro di voler eliminare questo articolo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Elimina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endcan
                    @endauth

                    <a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-dark">
                        <div class="ratio ratio-16x9">
                            <img src="{{ $article->featured_image ?? 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=1200&auto=format&fit=crop' }}" 
                                 class="card-img-top object-fit-cover" 
                                 alt="{{ $article->title }}">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-2">{{ $article->title }}</h5>
                            <p class="card-text text-muted small">{{ $article->excerpt }}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div class="text-muted small">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $article->published_at->format('d M Y') }}
                                </div>
                                <div class="text-muted small">
                                    <i class="far fa-user me-1"></i>
                                    {{ $article->user->name }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($article->tags->take(3) as $tag)
                                    <x-tag-badge :tag="$tag" />
                                @endforeach
                                @if($article->tags->count() > 3)
                                    <span class="badge bg-secondary">+{{ $article->tags->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun articolo trovato</h4>
                    <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                    @auth
                        <a href="{{ route('articles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Crea il primo articolo
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $articles->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
