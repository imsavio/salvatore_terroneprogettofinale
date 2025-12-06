<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="fw-semibold mt-3 mb-2">Messaggi</h1>
                <p class="text-muted mb-0">Chatta con i tuoi amici.</p>
            </div>
            <a href="{{ route('friends.index') }}" class="btn btn-outline-secondary btn-cta">
                <i class="bi bi-people me-2"></i> I miei amici
            </a>
        </div>

        @if($conversationUsers->count() > 0)
            <div class="card card-modern p-0" style="border: none; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                <div class="p-4 border-bottom" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;">
                    <h3 class="fw-semibold mb-0">Le tue conversazioni</h3>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @foreach($conversationUsers as $conv)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('messages.show', $conv['user']) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light hover-shadow" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 1px solid rgba(0, 0, 0, 0.05);">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold position-relative flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.375rem; box-shadow: 0 4px 12px rgba(0, 168, 255, 0.3);">
                                            {{ substr($conv['user']->name, 0, 1) }}
                                            @if($conv['unread_count'] > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                                    {{ $conv['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="mb-1 fw-semibold text-dark">{{ $conv['user']->name }}</h6>
                                            <small class="text-muted d-block" style="font-size: 0.8125rem;">ID: {{ $conv['user']->public_id }}</small>
                                            @if($conv['last_message'])
                                                <small class="text-muted d-block mt-2 {{ $conv['unread_count'] > 0 ? 'fw-semibold' : '' }}" style="font-size: 0.875rem; line-height: 1.4;">
                                                    {{ \Illuminate\Support\Str::limit($conv['last_message']->message, 50) }}
                                                </small>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                    {{ $conv['last_message']->created_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="card card-modern p-5 text-center" style="border: none; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                <div class="mb-3">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <p class="text-muted mb-3">Non hai ancora conversazioni.</p>
                <a href="{{ route('friends.index') }}" class="btn btn-primary btn-cta">
                    <i class="bi bi-people me-2"></i> Vai ai tuoi amici
                </a>
            </div>
        @endif
    </section>
</x-app-layout>

<style>
.hover-shadow:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-4px);
    border-color: rgba(0, 168, 255, 0.2) !important;
}
</style>
