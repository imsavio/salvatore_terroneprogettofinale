@props(['size' => 'md', 'text' => 'Loading...'])

@php
    $sizeClass = match($size) {
        'sm' => 'spinner-border-sm',
        'lg' => 'spinner-border-lg',
        default => ''
    };
@endphp

<div class="d-flex align-items-center justify-content-center {{ $attributes->get('class', 'py-5') }}">
    <div class="spinner-border {{ $sizeClass }} text-primary me-3" role="status">
        <span class="visually-hidden">{{ $text }}</span>
    </div>
    <span class="text-muted">{{ $text }}</span>
</div>

