<x-app-layout>
    <section class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="badge badge-soft">Pannello personale</span>
                <h1 class="fw-semibold mt-3 mb-1">Ciao, {{ auth()->user()->name }} 👋</h1>
                <p class="text-muted mb-0">Gestisci i tuoi contenuti e monitora le performance del blog.</p>
            </div>
            <a class="btn btn-primary btn-cta" href="{{ route('articles.create') }}">Nuovo articolo</a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Articoli totali</h6>
                    <h2 class="fw-bold mb-0">{{ $articleCount }}</h2>
                    <p class="text-muted small mb-0">Include bozze e articoli pubblicati.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Pubblicati</h6>
                    <h2 class="fw-bold mb-0">{{ $publishedCount }}</h2>
                    <p class="text-muted small mb-0">Ultimo aggiornamento {{ now()->translatedFormat('d F Y') }}.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-modern h-100 p-4">
                    <h6 class="text-uppercase text-muted mb-2">Prossima pubblicazione</h6>
                    <h2 class="fw-bold mb-0">{{ optional(optional($nextArticle)->published_at)->translatedFormat('d F') ?? '—' }}</h2>
                    <p class="text-muted small mb-0">{{ optional($nextArticle)->title ?? 'Nessun articolo programmato.' }}</p>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="section-heading mb-0">I tuoi ultimi articoli</h2>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('articles.index') }}">Vai alla lista</a>
        </div>

        <div class="row g-4">
            @forelse($latestArticles as $article)
                <div class="col-lg-4">
                    <div class="card card-modern h-100 p-4">
                        <span class="badge badge-soft w-auto mb-3">{{ $article->published_at ? 'Pubblicato' : 'Bozza' }}</span>
                        <h5 class="fw-semibold mb-2">{{ $article->title }}</h5>
                        <p class="text-muted flex-grow-1">{{ $article->excerpt }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">Aggiornato {{ $article->updated_at->shortAbsoluteDiffForHumans() }}</small>
                            <a class="btn btn-link" href="{{ route('articles.edit', $article) }}">Modifica</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-modern alert-info">
                        Hai appena iniziato! Crea il tuo primo articolo per popolare il dashboard.
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</x-app-layout>
