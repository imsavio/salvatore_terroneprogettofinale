<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="fw-semibold mt-3 mb-2">Notifiche</h1>
                <p class="text-muted mb-2" style="font-size: 1.125rem; line-height: 1.6;">Condividi la tua storia, in modo anonimo o con il tuo nome.</p>
                <p class="text-muted mb-0">Gestisci tutte le tue notifiche.</p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-cta">Segna tutte come lette</button>
                </form>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="card card-modern p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-semibold mb-0">Le tue notifiche</h3>
                    <span class="badge badge-soft">{{ $notifications->total() }} totali</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item border-0 px-0 py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    @if(isset($notification->data['type']) && $notification->data['type'] === 'friend_request')
                                        <i class="bi bi-person-plus"></i>
                                    @else
                                        <i class="bi bi-bell"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <p class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-semibold' }}">
                                                {{ $notification->data['message'] ?? 'Nuova notifica' }}
                                            </p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->read_at)
                                            <span class="badge bg-primary rounded-pill flex-shrink-0" style="width: 10px; height: 10px; padding: 0;"></span>
                                        @endif
                                    </div>
                                    @if(isset($notification->data['type']) && $notification->data['type'] === 'friend_request' && isset($notification->data['sender_id']))
                                        <div class="d-flex gap-2 mt-2">
                                            <a href="{{ route('friends.index') }}" class="btn btn-sm btn-primary">Vai alle richieste</a>
                                            @if(!$notification->read_at)
                                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="mb-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Segna come letta</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="mb-0" onsubmit="return confirm('Sei sicuro di voler eliminare questa notifica?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-0">
                        @endif
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        @else
            <div class="card card-modern p-5 text-center">
                <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mb-3 mt-3">Non hai ancora notifiche.</p>
            </div>
        @endif
    </section>
</x-app-layout>
