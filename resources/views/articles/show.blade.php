<x-app-layout>
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="badge badge-soft">{{ $article->isPublished() ? 'Pubblicato il '.$article->published_at?->translatedFormat('d M Y') : 'Bozza privata' }}</span>
                        <span class="text-muted">Autore: <strong>{{ $article->author_name }}</strong></span>
                    </div>
                    <div class="d-flex gap-2">
                        @can('update', $article)
                            <a href="{{ route('articles.edit', $article) }}" class="btn btn-outline-secondary btn-sm">Modifica</a>
                        @endcan
                        <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary btn-sm">Torna agli articoli</a>
                    </div>
                </div>

                <h1 class="fw-bold display-5 mb-4">{{ $article->title }}</h1>

                @if($article->cover_image)
                    <figure class="mb-5">
                        <img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}" class="img-fluid rounded-4 shadow-sm w-100">
                    </figure>
                @endif

                <div class="fs-5 lh-lg text-body">
                    @foreach(array_filter(preg_split("/\n\s*\n/", $article->body)) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap gap-2 mt-5">
                    @foreach($article->tags as $tag)
                        <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
