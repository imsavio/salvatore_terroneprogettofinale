@extends('layouts.app')

@section('title', 'Il Mio Profilo - ' . config('app.name'))
@section('description', 'Gestisci il tuo profilo e visualizza le tue statistiche.')

@section('breadcrumb')
    <x-breadcrumb :items="[['title' => 'Il Mio Profilo']]" />
@endsection

@section('content')
<div class="container">
    <!-- Profile Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <h1 class="h3 mb-2">{{ $user->name }}</h1>
                            <p class="text-muted mb-2">@{{ $user->username }}</p>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                            @if($user->bio)
                                <p class="mb-0">{{ $user->bio }}</p>
                            @else
                                <p class="text-muted mb-0">Nessuna biografia disponibile</p>
                            @endif
                        </div>
                        <div class="col-md-3 text-md-end">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary mb-2">
                                <i class="fas fa-edit me-2"></i>Modifica Profilo
                            </a>
                            <div class="text-muted small">
                                <i class="far fa-calendar me-1"></i>
                                Membro dal {{ $user->created_at->format('M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 text-primary mb-2">{{ $user->articles_count }}</div>
                    <div class="text-muted">Articoli Pubblicati</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 text-success mb-2">{{ $popularTags->count() }}</div>
                    <div class="text-muted">Tag Utilizzati</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 text-info mb-2">
                        {{ $user->articles()->published()->latest('published_at')->first()?->published_at?->format('d/m/Y') ?? 'N/A' }}
                    </div>
                    <div class="text-muted">Ultimo Articolo</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 text-warning mb-2">
                        {{ $user->articles()->published()->count() > 0 ? round($user->articles()->published()->avg('LENGTH(content)')) : 0 }}
                    </div>
                    <div class="text-muted">Media Caratteri</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Articles Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Articoli per Mese
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="articlesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Popular Tags -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>Tag Più Utilizzati
                    </h5>
                </div>
                <div class="card-body">
                    @if($popularTags->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($popularTags as $tag)
                                <a href="{{ route('tags.show', $tag) }}" class="text-decoration-none">
                                    <span class="badge fs-6 px-2 py-1" 
                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                        {{ $tag->name }} ({{ $tag->articles_count }})
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Nessun tag utilizzato ancora</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- User's Articles -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-newspaper me-2 text-primary"></i>I Miei Articoli
                    </h5>
                </div>
                <div class="card-body">
                    @if($articles->count() > 0)
                        <div class="row g-4">
                            @foreach($articles as $article)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card article-card h-100 border-0 shadow-sm">
                                        <a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-dark">
                                            <div class="ratio ratio-16x9">
                                                <img src="{{ $article->featured_image ?? 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=1200&auto=format&fit=crop' }}" 
                                                     class="card-img-top object-fit-cover" 
                                                     alt="{{ $article->title }}">
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title fw-semibold mb-2">{{ $article->title }}</h6>
                                                <p class="card-text text-muted small">{{ Str::limit($article->excerpt, 100) }}</p>
                                            </div>
                                            <div class="card-footer bg-white border-0">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                    <div class="text-muted small">
                                                        <i class="far fa-calendar me-1"></i>
                                                        {{ $article->published_at->format('d M Y') }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        <i class="far fa-eye me-1"></i>
                                                        {{ $article->published_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($article->tags->take(3) as $tag)
                                                        <span class="badge fs-6 px-2 py-1" 
                                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                                            {{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($article->tags->count() > 3)
                                                        <span class="badge bg-secondary fs-6 px-2 py-1">
                                                            +{{ $article->tags->count() - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($articles->hasPages())
                            <div class="mt-4">
                                {{ $articles->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nessun articolo pubblicato</h4>
                            <p class="text-muted">Inizia a scrivere il tuo primo articolo!</p>
                            <a href="{{ route('articles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Crea il Primo Articolo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('articlesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($articleStats['labels']),
            datasets: [{
                label: 'Articoli Pubblicati',
                data: @json($articleStats['data']),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#3B82F6'
                }
            }
        }
    });
});
</script>

<style>
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
    .h3 {
        font-size: 1.5rem;
    }
    
    .h2 {
        font-size: 1.75rem;
    }
}
</style>
@endsection








