@extends('layouts.app')

@section('title', 'Articoli - ' . config('app.name', 'Laravel Blog'))

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center my-5">
        <div class="col-lg-7">
            <h1 class="display-4 fw-bold text-primary mb-3">Articoli del Blog</h1>
            <p class="lead text-muted">Scopri tutti i nostri articoli più recenti su tecnologia, programmazione e molto altro.</p>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-end">
            <img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=1200&auto=format&fit=crop" class="img-fluid rounded shadow" alt="Blog Hero">
        </div>
    </div>

    <div class="row g-4">
        <!-- Articles Grid -->
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse ($articles as $article)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('articles.show', $article) }}" class="text-decoration-none">
                            <x-article-card :article="$article" />
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Nessun articolo disponibile al momento.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $articles->onEachSide(1)->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tag Popolari</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($topTags as $tag)
                            <a href="#" class="text-decoration-none">
                                <x-tag-badge :tag="$tag" :show-count="true" />
                            </a>
                        @empty
                            <span class="text-muted small">No tags yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">About</h5>
                    <p class="text-muted mb-0">Welcome to our modern blog where we share knowledge about Laravel, PHP, JavaScript, and more.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


