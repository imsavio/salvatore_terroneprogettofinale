<x-app-layout>
    <section class="container py-5">
        <div class="mb-4">
            <span class="badge badge-soft">Nuovo contenuto</span>
            <h1 class="fw-semibold mt-3 mb-2">Crea un nuovo articolo</h1>
            <p class="text-muted">Racconta un'idea con un linguaggio chiaro e coinvolgente. Il design farà il resto.</p>
        </div>

        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @include('articles.partials.form', [
                'article' => $article,
                'tagList' => $tagList,
                'submitLabel' => 'Pubblica articolo',
            ])
        </form>
    </section>
</x-app-layout>

