<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-5 gap-3">
            <div>
                <span class="badge badge-soft">Raccolta</span>
                <h1 class="fw-semibold mt-3 mb-2">Articoli</h1>
                <p class="text-muted mb-0">Approfondimenti curati dalla community su tecnologia, innovazione e design.</p>
            </div>
            @auth
                <a href="{{ route('articles.create') }}" class="btn btn-primary btn-cta">Nuovo articolo</a>
            @endauth
        </div>

        <div class="row g-4">
            @forelse($articles as $article)
                <div class="col-lg-4">
                    <article class="card card-modern h-100">
                        @if($article->cover_image)
                            <img src="{{ asset('storage/'.$article->cover_image) }}" class="card-img-top" alt="{{ $article->title }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge badge-soft">{{ $article->isPublished() ? $article->published_at?->translatedFormat('d M Y') : 'Bozza' }}</span>
                                <small class="text-muted">{{ $article->author->name }}</small>
                            </div>
                            <h2 class="h4 fw-semibold">{{ $article->title }}</h2>
                            <p class="text-muted flex-grow-1">{{ $article->excerpt }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach($article->tags->take(3) as $tag)
                                        <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                                <a href="{{ route('articles.show', $article) }}" class="btn btn-link">Leggi</a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-modern alert-info">Ancora nessun articolo disponibile. Torna presto!</div>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $articles->links() }}
        </div>
    </section>
</x-app-layout>

