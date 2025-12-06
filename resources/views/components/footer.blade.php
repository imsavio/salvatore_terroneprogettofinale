<footer class="mt-5 py-5" role="contentinfo">
    <div class="container content-container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted">© {{ date('Y') }} {{ config('app.name', 'Laravel Blog') }}</div>
        <ul class="nav gap-3">
            <li class="nav-item"><a class="nav-link px-0 text-muted" href="{{ route('home') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link px-0 text-muted" href="{{ route('tags.index') }}">Tag</a></li>
            <li class="nav-item"><a class="nav-link px-0 text-muted" href="{{ route('contact') }}">Contatti</a></li>
        </ul>
    </div>
</footer>











