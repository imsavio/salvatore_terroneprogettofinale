@extends('layouts.app')

@section('title', 'Articoli - ' . config('app.name', 'Laravel Blog'))

@section('content')
<section class="py-5 py-lg-6">
    <div class="row align-items-end gy-3">
        <div class="col-lg-8">
            <small class="text-uppercase text-muted">Magazine</small>
            <h1 class="display-5 fw-semibold mb-2">Articoli del Blog</h1>
            <p class="fs-5 text-muted mb-0">Tecnologia, programmazione e design. Curati con attenzione.</p>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="row g-4">
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse ($articles as $article)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('articles.show', $article) }}" class="text-decoration-none">
                            <x-article-card :article="$article" />
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="border rounded-3 p-4 text-center text-muted">Nessun articolo disponibile al momento.</div>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $articles->onEachSide(1)->links() }}
            </div>
        </div>
        <div class="col-lg-3">
            <div class="border rounded-3 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase small text-muted">Tag popolari</span>
                    <a class="small" href="{{ route('tags.index') }}">Tutti</a>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($topTags as $tag)
                        <a href="#" class="text-decoration-none">
                            <x-tag-badge :tag="$tag" :show-count="true" />
                        </a>
                    @empty
                        <span class="text-muted small">Nessun tag.</span>
                    @endforelse
                </div>
            </div>
            <div class="border rounded-3 p-3">
                <span class="text-uppercase small text-muted">About</span>
                <p class="mt-2 mb-0 text-muted">Un blog essenziale su Laravel, PHP e sviluppo moderno.</p>
            </div>
        </div>
    </div>
</section>
@endsection


