@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <i class="fas fa-home me-1"></i>Home
            </a>
        </li>
        @foreach($items as $item)
            @if(isset($item['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}" class="text-decoration-none">{{ $item['title'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $item['title'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>

