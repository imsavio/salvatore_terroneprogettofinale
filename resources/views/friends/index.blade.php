<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="fw-semibold mt-3 mb-2">I miei amici</h1>
                <p class="text-muted mb-0">Gestisci le tue amicizie e trova nuove persone da seguire.</p>
            </div>
            <a href="{{ route('friends.search') }}" class="btn btn-primary btn-cta">Cerca amici</a>
        </div>

        <!-- Richieste in arrivo -->
        @if($friendRequests->count() > 0)
            <div class="card card-modern p-4 mb-4">
                <h3 class="fw-semibold mb-3">Richieste in arrivo</h3>
                <div class="row g-3">
                    @foreach($friendRequests as $request)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ substr($request->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold">{{ $request->name }}</h6>
                                    <small class="text-muted d-block">ID: {{ $request->public_id }}</small>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    <form action="{{ route('friends.accept', $request) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Accetta</button>
                                    </form>
                                    <form action="{{ route('friends.reject', $request) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Rifiuta</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $friendRequests->links() }}
                </div>
            </div>
        @endif

        <!-- Richieste inviate -->
        @if($sentRequests->count() > 0)
            <div class="card card-modern p-4 mb-4">
                <h3 class="fw-semibold mb-3">Richieste inviate</h3>
                <div class="row g-3">
                    @foreach($sentRequests as $request)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ substr($request->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold">{{ $request->name }}</h6>
                                    <small class="text-muted d-block">ID: {{ $request->public_id }}</small>
                                    <span class="badge badge-soft">In attesa</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $sentRequests->links() }}
                </div>
            </div>
        @endif

        <!-- Lista amici -->
        <div class="card card-modern p-4">
            <h3 class="fw-semibold mb-3">I miei amici ({{ $friends->total() }})</h3>
            @if($friends->count() > 0)
                <div class="row g-3">
                    @foreach($friends as $friend)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ substr($friend->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold">{{ $friend->name }}</h6>
                                    <small class="text-muted d-block">ID: {{ $friend->public_id }}</small>
                                    <small class="text-muted d-block">{{ $friend->articles()->published()->count() }} storie pubblicate</small>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    <a href="{{ route('messages.show', $friend) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-chat-dots"></i> Chat
                                    </a>
                                    <form action="{{ route('friends.remove', $friend) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler rimuovere {{ $friend->name }} dagli amici?');" class="mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Rimuovi</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $friends->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted mb-3">Non hai ancora amici. Inizia a cercare!</p>
                    <a href="{{ route('friends.search') }}" class="btn btn-primary btn-cta">Cerca amici</a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
