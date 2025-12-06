<x-app-layout>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container py-5">
            <div class="row align-items-center gy-5">
                <div class="col-lg-7 fade-in-up">
                    <h1 class="hero-title mb-4">Condividi la tua storia,<br>in modo anonimo o con il tuo nome.</h1>
                    <p class="hero-subtitle mb-5">Ogni vita merita di essere raccontata. Pubblica la tua storia, le tue esperienze, i tuoi momenti. Scegli se farlo in modo anonimo o con il tuo nome.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('articles.index') }}" class="btn btn-primary btn-cta">Leggi le storie</a>
                        @auth
                            <a href="{{ route('articles.create') }}" class="btn btn-outline-secondary btn-cta">Raccontati</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-cta">Inizia ora</a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-modern text-dark bg-light p-4">
                        <h5 class="fw-semibold mb-3">Statistiche in tempo reale</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-white h-100">
                                    <span class="text-muted text-uppercase small">Articoli</span>
                                    <h3 class="fw-bold mt-2 mb-0">{{ $metrics['articles'] }}</h3>
                                    <small class="text-muted">Pubblicati nell'ultimo mese</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-white h-100">
                                    <span class="text-muted text-uppercase small">Autori</span>
                                    <h3 class="fw-bold mt-2 mb-0">{{ $metrics['authors'] }}</h3>
                                    <small class="text-muted">Attivi sulla piattaforma</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded-4 bg-white">
                                    <span class="text-muted text-uppercase small">Temi più letti</span>
                                    <div class="d-flex gap-2 flex-wrap mt-3">
                                        @forelse($metrics['topTags'] as $tag)
                                            <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary">{{ $tag }}</span>
                                        @empty
                                            <span class="text-muted small">Ancora nessun tag popolare.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Articles Section - si sovrappone -->
    <section class="overlap-section">
        <div class="container py-5">
            <div class="row align-items-center mb-5">
                <div class="col-md-8">
                    <h2 class="section-heading">Storie in evidenza</h2>
                    <p class="section-subtitle mb-0">Storie condivise dalla community, momenti di vita da scoprire.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Esplora tutto</a>
                </div>
            </div>

            <div class="row g-4">
                @forelse($featuredArticles as $article)
                    <div class="col-lg-4">
                        <article class="card card-modern h-100">
                            @if($article->cover_image)
                                <img src="{{ asset('storage/'.$article->cover_image) }}" class="card-img-top" alt="{{ $article->title }}">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <span class="badge badge-soft mb-3">{{ $article->published_at?->translatedFormat('d M Y') ?? 'Bozza' }}</span>
                                <h3 class="h4 fw-semibold">{{ $article->title }}</h3>
                                <p class="text-muted flex-grow-1">{{ $article->excerpt }}</p>
                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light" style="width: 42px; height: 42px;"></div>
                                        <div>
                                            <small class="text-muted d-block">Autore</small>
                                            <span class="fw-semibold">{{ $article->author_name }}</span>
                                        </div>
                                    </div>
                                    <a class="btn btn-link" href="{{ route('articles.show', $article) }}">Leggi</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-modern alert-info">Nessun articolo pubblicato al momento. Torna presto!</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Features Section - si sovrappone -->
    <section class="overlap-section overlap-section-light">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-heading">Uno spazio sicuro per condividere</h2>
                    <p class="section-subtitle">Racconta la tua vita in totale libertà. Scegli se farlo in modo anonimo o con il tuo nome. Ogni storia è importante.</p>
                    <ul class="list-unstyled mt-5" style="color: #6e6e73;">
                        <li class="d-flex align-items-center mb-3" style="font-size: 1.125rem;"><i class="bi bi-shield-lock-fill text-primary me-3" style="font-size: 1.5rem;"></i> Pubblica in modo anonimo o con il tuo nome</li>
                        <li class="d-flex align-items-center mb-3" style="font-size: 1.125rem;"><i class="bi bi-heart-fill text-primary me-3" style="font-size: 1.5rem;"></i> Condividi le tue esperienze e i tuoi momenti</li>
                        <li class="d-flex align-items-center" style="font-size: 1.125rem;"><i class="bi bi-people-fill text-primary me-3" style="font-size: 1.5rem;"></i> Leggi le storie di altri e condividi la tua</li>
                    </ul>
                </div>
                <div class="col-lg-5 ms-lg-auto">
                    <div class="card card-modern p-5">
                        <h4 class="fw-semibold mb-3" style="font-size: 1.75rem;">Hai una storia da raccontare?</h4>
                        <p class="text-muted mb-4" style="font-size: 1.125rem;">Ogni vita è unica e merita di essere condivisa. Inizia a raccontare la tua storia oggi.</p>
                        <a href="{{ route('articles.create') }}" class="btn btn-primary btn-cta">Inizia a scrivere</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

