@props(['tag', 'showCount' => false])

<span class="badge rounded-pill" 
      style="background-color: {{ $tag->color }}33; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}22;">
    #{{ $tag->name }}
    @if($showCount && isset($tag->articles_count))
        <span class="text-muted">({{ $tag->articles_count }})</span>
    @endif
</span>

