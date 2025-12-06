@props(['limit' => 8])

@php
    $popularTags = \App\Models\Tag::withCount('articles')
        ->whereHas('articles')
        ->orderBy('articles_count', 'desc')
        ->limit($limit)
        ->get();
@endphp

@if($popularTags->count() > 0)
    <section class="mt-5" aria-labelledby="popular-tags-title">
        <div class="container content-container">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h2 id="popular-tags-title" class="h5 mb-0">Tag popolari</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="text-decoration-none">
                                <span class="badge fs-6 px-2 py-1" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                    {{ $tag->name }} ({{ $tag->articles_count }})
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('tags.index') }}" class="btn btn-outline-primary btn-sm">Vedi tutti i tag</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif











