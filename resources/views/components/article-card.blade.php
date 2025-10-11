@props(['article'])

<div class="card article-card h-100 border-0 shadow-sm">
        <div class="ratio ratio-16x9">
            <img src="{{ $article->featured_image ?? 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=400&auto=format&fit=crop' }}" 
                 data-src="{{ $article->featured_image ?? 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=800&auto=format&fit=crop' }}"
                 class="card-img-top object-fit-cover lazy" 
                 alt="{{ $article->title }}"
                 loading="lazy"
                 width="400"
                 height="225">
        </div>
    <div class="card-body">
        <h5 class="card-title text-dark fw-semibold">{{ $article->title }}</h5>
        <p class="card-text text-muted">{{ $article->excerpt }}</p>
    </div>
    <div class="card-footer bg-white border-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted small">
                <i class="far fa-calendar me-1"></i>
                {{ optional($article->published_at)->format('d M Y') }}
                <span class="mx-1">•</span>
                <i class="far fa-user me-1"></i>
                {{ $article->user->name }}
            </div>
        </div>
        <div class="mt-2 d-flex flex-wrap gap-2">
            @foreach ($article->tags as $tag)
                <x-tag-badge :tag="$tag" />
            @endforeach
        </div>
    </div>
</div>

