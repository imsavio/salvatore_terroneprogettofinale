@extends('layouts.app')

@section('title', $tag->name . ' - Tag - ' . config('app.name'))
@section('description', 'Articoli con il tag: ' . $tag->name)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Tag', 'url' => route('tags.index')],
        ['title' => $tag->name]
    ]" />
@endsection

@section('content')
<div class="container">
    <!-- Tag Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <div class="mb-3">
                    <div class="tag-circle-large mx-auto" 
                         style="background-color: {{ $tag->color }}20; border: 4px solid {{ $tag->color }};">
                        <i class="fas fa-tag" style="color: {{ $tag->color }};"></i>
                    </div>
                </div>
                <h1 class="display-4 fw-bold text-dark mb-3">{{ $tag->name }}</h1>
                <p class="lead text-muted mb-0">
                    {{ $articles->total() }} {{ Str::plural('articolo', $articles->total()) }} con questo tag
                </p>
            </div>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="row g-4">
        @forelse ($articles as $article)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card article-card h-100 border-0 shadow-sm">
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
                                @foreach ($article->tags->take(3) as $articleTag)
                                    @if($articleTag->id === $tag->id)
                                        <span class="badge fs-6 px-2 py-1" 
                                              style="background-color: {{ $articleTag->color }}20; color: {{ $articleTag->color }}; border: 1px solid {{ $articleTag->color }}40;">
                                            {{ $articleTag->name }}
                                        </span>
                                    @else
                                        <a href="{{ route('tags.show', $articleTag) }}" 
                                           class="text-decoration-none">
                                            <span class="badge bg-secondary fs-6 px-2 py-1">
                                                {{ $articleTag->name }}
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                                @if($article->tags->count() > 3)
                                    <span class="badge bg-light text-dark fs-6 px-2 py-1">
                                        +{{ $article->tags->count() - 3 }}
                                    </span>
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
                    <p class="text-muted">Non ci sono ancora articoli con questo tag</p>
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

    <!-- Related Tags -->
    @if($articles->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2 text-primary"></i>Tag Correlati
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $relatedTags = \App\Models\Tag::whereHas('articles', function($query) use ($tag) {
                                    $query->whereHas('tags', function($q) use ($tag) {
                                        $q->where('tags.id', $tag->id);
                                    });
                                })
                                ->where('id', '!=', $tag->id)
                                ->withCount('articles')
                                ->orderBy('articles_count', 'desc')
                                ->limit(10)
                                ->get();
                            @endphp
                            
                            @foreach($relatedTags as $relatedTag)
                                <a href="{{ route('tags.show', $relatedTag) }}" 
                                   class="text-decoration-none">
                                    <span class="badge fs-6 px-3 py-2" 
                                          style="background-color: {{ $relatedTag->color }}20; color: {{ $relatedTag->color }}; border: 1px solid {{ $relatedTag->color }}40;">
                                        {{ $relatedTag->name }} ({{ $relatedTag->articles_count }})
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.tag-circle-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    transition: transform 0.2s ease;
}

.tag-circle-large:hover {
    transform: scale(1.05);
}

.article-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
}

.object-fit-cover {
    object-fit: cover;
}

.badge {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tag-circle-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}
</style>
@endsection

