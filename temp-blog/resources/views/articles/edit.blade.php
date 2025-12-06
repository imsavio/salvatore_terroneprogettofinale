<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <span class="badge badge-soft">Modifica</span>
                <h1 class="fw-semibold mt-3 mb-2">Aggiorna "{{ $article->title }}"</h1>
                <p class="text-muted">Apporta miglioramenti al tuo articolo e mantieni aggiornata la community.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-secondary">Visualizza</a>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Torna alla lista</a>
            </div>
        </div>

        <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @include('articles.partials.form', [
                'article' => $article,
                'tagList' => $tagList,
                'submitLabel' => 'Salva modifiche',
            ])
        </form>

        <div class="mt-4">
            <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Elimina articolo</button>
            </form>
        </div>
    </section>
</x-app-layout>

