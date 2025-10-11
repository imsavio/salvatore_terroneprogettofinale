@extends('layouts.app')

@section('title', $article->title . ' - ' . config('app.name'))
@section('description', $article->excerpt)
@section('keywords', $article->tags->pluck('name')->join(', ') . ', ' . config('app.name'))
@section('og_type', 'article')
@section('og_image', $article->featured_image ?? asset('images/og-default.jpg'))
@section('twitter_site', '@laravelblog')
@section('twitter_creator', '@laravelblog')

@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $article->title }}",
  "description": "{{ $article->excerpt }}",
  "image": "{{ $article->featured_image ?? asset('images/og-default.jpg') }}",
  "author": {
    "@type": "Person",
    "name": "{{ $article->user->name ?? 'Autore Sconosciuto' }}"
    @if($article->user)
    ,"url": "{{ route('users.show', $article->user) }}"
    @endif
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ config('app.name') }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "datePublished": "{{ $article->published_at->toISOString() }}",
  "dateModified": "{{ $article->updated_at->toISOString() }}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "keywords": "{{ $article->tags->pluck('name')->join(', ') }}",
  "articleSection": "Technology",
  "wordCount": "{{ str_word_count(strip_tags($article->content)) }}"
}
</script>
@endsection

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Articoli', 'url' => route('home')],
        ['title' => $article->title]
    ]" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <article class="mb-5">
                <!-- Article Header -->
                <header class="mb-5">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="display-4 fw-bold text-dark mb-0">{{ $article->title }}</h1>
                        
                        <!-- Action buttons for owners -->
                        @auth
                            @can('update', $article)
                                <div class="btn-group" role="group">
                                    <a href="{{ route('articles.edit', $article) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Modifica articolo">
                                        <i class="fas fa-edit me-1"></i>Modifica
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Sei sicuro di voler eliminare questo articolo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-sm" 
                                                title="Elimina articolo">
                                            <i class="fas fa-trash me-1"></i>Elimina
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        @endauth
                    </div>
                    
                    <!-- Article Meta -->
                    <div class="d-flex flex-wrap align-items-center text-muted mb-4">
                        <div class="d-flex align-items-center me-4 mb-2">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white small"></i>
                            </div>
                            <span class="fw-medium">{{ $article->user->name }}</span>
                        </div>
                        <div class="d-flex align-items-center me-4 mb-2">
                            <i class="far fa-calendar me-2"></i>
                            <span>{{ $article->published_at->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center me-4 mb-2">
                            <i class="far fa-clock me-2"></i>
                            <span>{{ $article->published_at->format('H:i') }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-eye me-2"></i>
                            <span>{{ $article->published_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($article->tags->count() > 0)
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('home', ['tag' => $tag->slug]) }}" 
                                   class="text-decoration-none">
                                    <x-tag-badge :tag="$tag" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </header>

                <!-- Featured Image -->
                @if($article->featured_image)
                    <div class="mb-5">
                        <img src="{{ $article->featured_image }}" 
                             class="img-fluid rounded-3 shadow-sm w-100" 
                             alt="{{ $article->title }}"
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="article-content">
                    <div class="prose">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>

                <!-- Article Footer -->
                <footer class="mt-5 pt-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 48px; height: 48px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $article->user->name }}</h6>
                                    <small class="text-muted">Autore dell'articolo</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div class="text-muted small">
                                <i class="far fa-calendar me-1"></i>
                                Pubblicato il {{ $article->published_at->format('d M Y \a\l\l\e H:i') }}
                            </div>
                        </div>
                    </div>
                </footer>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Article Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Statistiche
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="h4 text-primary mb-1">{{ $article->tags->count() }}</div>
                            <small class="text-muted">Tag</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-success mb-1">{{ strlen($article->content) }}</div>
                            <small class="text-muted">Caratteri</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-info mb-1">{{ str_word_count($article->content) }}</div>
                            <small class="text-muted">Parole</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Author Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2 text-primary"></i>Autore
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $article->user->name }}</h6>
                            <small class="text-muted">Autore</small>
                        </div>
                    </div>
                    <a href="{{ route('home', ['author' => $article->user->id]) }}" 
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-newspaper me-2"></i>Vedi altri articoli
                    </a>
                </div>
            </div>

            <!-- Related Tags -->
            @if($article->tags->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2 text-primary"></i>Tag Correlati
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('home', ['tag' => $tag->slug]) }}" 
                                   class="text-decoration-none">
                                    <x-tag-badge :tag="$tag" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Back to Articles -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Torna agli Articoli
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prose {
    line-height: 1.7;
    font-size: 1.1rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.prose h1 { font-size: 2rem; }
.prose h2 { font-size: 1.75rem; }
.prose h3 { font-size: 1.5rem; }
.prose h4 { font-size: 1.25rem; }

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose blockquote {
    border-left: 4px solid #0d6efd;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
}

.prose code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.prose pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.prose pre code {
    background: none;
    padding: 0;
}
</style>
@endsection
