<x-app-layout>
    <section class="container py-5">
        <div class="mb-4">
            <h1 class="fw-semibold mt-3 mb-2">Raccontati</h1>
            <p class="text-muted">Condividi un momento, un'esperienza, una parte della tua vita. Scegli se farlo in modo anonimo o con il tuo nome.</p>
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

        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
            @csrf
            @include('articles.partials.form', [
                'article' => $article,
                'tagList' => $tagList,
                'submitLabel' => 'Pubblica articolo',
            ])
        </form>
    </section>
</x-app-layout>

