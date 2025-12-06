<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <span class="badge badge-soft">Modifica</span>
                <h1 class="fw-semibold mt-3 mb-2">Aggiorna "{{ $article->title }}"</h1>
                <p class="text-muted">Apporta miglioramenti alla tua storia e mantieni aggiornata la community.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-secondary">Visualizza</a>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Torna alla lista</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <h5 class="fw-semibold mb-2">Si sono verificati i seguenti errori:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            @include('articles.partials.form', [
                'article' => $article,
                'tagList' => $tagList,
                'submitLabel' => 'Salva modifiche',
            ])
        </form>

        <div class="mt-2">
            <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="border-radius: 24px; padding: 0.75rem 2rem;">Elimina articolo</button>
            </form>
        </div>
    </section>
</x-app-layout>

