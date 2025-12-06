<x-app-layout>
    <section class="hero">
        <div class="container py-5">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <span class="badge badge-soft mb-3 text-uppercase">NovaBlog</span>
                    <h1 class="hero-title mb-3">Racconta idee dal futuro, oggi.</h1>
                    <p class="hero-subtitle mb-4">Un'esperienza di scrittura pensata per creativi, startup e visionari. Condividi le tue storie con un design pulito, veloce e mobile-first.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('articles.index') }}" class="btn btn-primary btn-cta">Leggi gli articoli</a>
                        @auth
                            <a href="{{ route('articles.create') }}" class="btn btn-outline-light btn-cta">Crea il tuo</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-cta">Inizia ora</a>
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

    <section class="container py-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="section-heading">Articoli in evidenza</h2>
                <p class="section-subtitle mb-0">Curati dalla community, selezionati per eleganza e contenuto.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary">Esplora tutto</a>
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
                                        <span class="fw-semibold">{{ $article->author->name }}</span>
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
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-heading">Pensato per performance e accessibilità</h2>
                    <p class="section-subtitle">Laravel 12, Bootstrap 5 e componenti ottimizzati per offrire un'esperienza rapida su qualsiasi dispositivo.</p>
                    <ul class="list-unstyled mt-4 text-muted">
                        <li class="d-flex align-items-center mb-2"><i class="bi bi-lightning-charge-fill text-primary me-2"></i> Deploy immediato con database SQLite o MySQL</li>
                        <li class="d-flex align-items-center mb-2"><i class="bi bi-shield-check text-primary me-2"></i> Autenticazione sicura e gestione utenti</li>
                        <li class="d-flex align-items-center"><i class="bi bi-layout-text-sidebar-reverse text-primary me-2"></i> CRUD articoli completo con editor intuitivo</li>
                    </ul>
                </div>
                <div class="col-lg-5 ms-lg-auto">
                    <div class="card card-modern p-4">
                        <h4 class="fw-semibold mb-3">Vuoi collaborare?</h4>
                        <p class="text-muted">Siamo alla ricerca di autori e contributor. Contattaci e raccontaci il tuo progetto.</p>
                        <a href="{{ route('contact.show') }}" class="btn btn-primary btn-cta">Scrivici</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

