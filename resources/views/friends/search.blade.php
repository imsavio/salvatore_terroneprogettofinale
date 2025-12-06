<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="fw-semibold mt-3 mb-2">Cerca amici</h1>
                <p class="text-muted mb-0">Cerca utenti per nome, email o ID pubblico.</p>
            </div>
            <a href="{{ route('friends.index') }}" class="btn btn-outline-secondary btn-cta">I miei amici</a>
        </div>

        <div class="card card-modern p-4 mb-4">
            <form action="{{ route('friends.search') }}" method="GET" class="d-flex gap-3">
                <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Cerca per nome, email o ID pubblico..." autofocus>
                <button type="submit" class="btn btn-primary btn-cta">Cerca</button>
            </form>
        </div>

        @if($query)
            @if($users->count() > 0)
                <div class="card card-modern p-4">
                    <h3 class="fw-semibold mb-3">Risultati della ricerca ({{ $users->total() }})</h3>
                    <div class="row g-3">
                        @foreach($users as $user)
                            @php
                                $isFriend = \DB::table('friendships')
                                    ->where('status', 'accepted')
                                    ->where(function ($q) use ($user) {
                                        $q->where('user_id', auth()->id())
                                          ->where('friend_id', $user->id);
                                    })
                                    ->orWhere(function ($q) use ($user) {
                                        $q->where('user_id', $user->id)
                                          ->where('friend_id', auth()->id());
                                    })
                                    ->exists();
                                $hasPendingRequest = \DB::table('friendships')
                                    ->where('status', 'pending')
                                    ->where(function ($q) use ($user) {
                                        $q->where('user_id', auth()->id())
                                          ->where('friend_id', $user->id);
                                    })
                                    ->orWhere(function ($q) use ($user) {
                                        $q->where('user_id', $user->id)
                                          ->where('friend_id', auth()->id());
                                    })
                                    ->exists();
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                        <small class="text-muted d-block">ID: {{ $user->public_id }}</small>
                                        <small class="text-muted d-block">{{ $user->articles()->published()->count() }} storie pubblicate</small>
                                    </div>
                                    <div class="flex-shrink-0" style="min-width: 100px; display: flex; align-items: center; justify-content: flex-end;">
                                        @if($isFriend)
                                            <span class="badge badge-soft d-inline-flex align-items-center" style="min-height: 32px;">Amico</span>
                                        @elseif($hasPendingRequest)
                                            <span class="badge badge-soft d-inline-flex align-items-center" style="min-height: 32px;">Richiesta inviata</span>
                                        @else
                                            <form action="{{ route('friends.add', $user) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" style="min-height: 32px;">Aggiungi</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            @else
                <div class="card card-modern p-5 text-center">
                    <p class="text-muted mb-0">Nessun utente trovato per "{{ $query }}".</p>
                </div>
            @endif
        @else
            <div class="card card-modern p-5 text-center">
                <p class="text-muted mb-0">Inserisci un termine di ricerca per trovare utenti.</p>
            </div>
        @endif
    </section>
</x-app-layout>
