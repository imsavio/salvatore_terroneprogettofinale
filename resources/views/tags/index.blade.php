@extends('layouts.app')

@section('title', 'Tag - ' . config('app.name'))
@section('description', 'Esplora tutti i tag del blog e scopri gli articoli per categoria.')

@section('breadcrumb')
    <x-breadcrumb :items="[['title' => 'Tag']]" />
@endsection

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-4 fw-bold text-dark mb-3">Tag</h1>
                <p class="lead text-muted mb-0">Esplora i contenuti per categoria</p>
            </div>
        </div>
    </div>

    <!-- Tags Grid -->
    <div class="row g-4">
        @forelse ($tags as $tag)
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 tag-card">
                    <div class="card-body text-center p-4">
                        <!-- Tag Color Circle -->
                        <div class="mb-3">
                            <div class="tag-circle mx-auto" style="background-color: {{ $tag->color }}20; border: 3px solid {{ $tag->color }};">
                                <i class="fas fa-tag" style="color: {{ $tag->color }};"></i>
                            </div>
                        </div>

                        <!-- Tag Name -->
                        <h5 class="card-title fw-bold mb-2">
                            <a href="{{ route('tags.show', $tag) }}" 
                               class="text-decoration-none text-dark">
                                {{ $tag->name }}
                            </a>
                        </h5>

                        <!-- Article Count -->
                        <div class="mb-3">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                <i class="fas fa-newspaper me-1"></i>
                                {{ $tag->articles_count }} {{ $tag->articles_count == 1 ? 'articolo' : 'articoli' }}
                            </span>
                        </div>

                        <!-- Tag Description -->
                        <p class="text-muted small mb-3">
                            {{ Str::limit('Esplora tutti gli articoli con questo tag', 50) }}
                        </p>

                        <!-- Action Button -->
                        <a href="{{ route('tags.show', $tag) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>
                            Visualizza Articoli
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun tag disponibile</h4>
                    <p class="text-muted">I tag verranno creati automaticamente quando pubblichi articoli</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Popular Tags Section -->
    @if($tags->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-fire me-2 text-danger"></i>Tag Più Popolari
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags->take(10) as $tag)
                                <a href="{{ route('tags.show', $tag) }}" 
                                   class="text-decoration-none">
                                    <span class="badge fs-6 px-3 py-2" 
                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                        {{ $tag->name }} ({{ $tag->articles_count }})
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
.tag-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.tag-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
}

.tag-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    transition: transform 0.2s ease;
}

.tag-card:hover .tag-circle {
    transform: scale(1.1);
}

.badge {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tag-circle {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}
</style>
@endsection

