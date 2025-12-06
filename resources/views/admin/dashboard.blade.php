<x-app-layout>
    <section class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="badge badge-soft bg-primary-subtle text-primary">Pannello amministratore</span>
                <h1 class="fw-semibold mt-3 mb-1">Ciao, {{ auth()->user()->name }}</h1>
                <p class="text-muted mb-0" style="font-size: 1.05rem;">
                    Da qui puoi avere una panoramica rapida di utenti, articoli e attività sul blog.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Utenti</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['users'] }}</h2>
                    <p class="text-muted small mb-0">Totale utenti registrati.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Articoli</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['articles'] }}</h2>
                    <p class="text-muted small mb-0">Tutti gli articoli creati.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Pubblicati</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['publishedArticles'] }}</h2>
                    <p class="text-muted small mb-0">Articoli visibili sul sito.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Tag</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['tags'] }}</h2>
                    <p class="text-muted small mb-0">Categorie e argomenti usati.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-modern h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Ultimi utenti registrati</h2>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @forelse($latestUsers as $user)
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                                <span class="badge {{ $user->isAdmin() ? 'bg-primary' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $user->isAdmin() ? 'Admin' : 'Utente' }}
                                </span>
                            </li>
                        @empty
                            <li class="text-muted small">Nessun utente trovato.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-modern h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Ultimi articoli</h2>
                        <a href="{{ route('articles.index') }}" class="btn btn-sm btn-outline-secondary">Vai agli articoli</a>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @forelse($latestArticles as $article)
                            <li class="py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $article->title }}</strong>
                                        <div class="text-muted small">
                                            di {{ $article->author_name }} •
                                            {{ $article->published_at ? 'Pubblicato '.$article->published_at->diffForHumans() : 'Bozza' }}
                                        </div>
                                    </div>
                                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                                </div>
                            </li>
                        @empty
                            <li class="text-muted small">Nessun articolo trovato.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>


