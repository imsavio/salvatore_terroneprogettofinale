<x-app-layout>
    <section class="container py-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Indietro
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="fw-semibold mb-0" style="font-size: 1.5rem;">{{ $user->name }}</h1>
                            <small class="text-muted">ID: {{ $user->public_id }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-modern p-0" style="height: 650px; display: flex; flex-direction: column; border: none; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
            <!-- Area messaggi -->
            <div class="flex-grow-1 p-4" style="overflow-y: auto; background: linear-gradient(180deg, #f5f5f7 0%, #ffffff 100%);" id="messages-container">
                @if($messages->count() > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($messages as $message)
                            <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="d-flex align-items-end gap-2" style="max-width: 75%;">
                                    @if($message->sender_id !== auth()->id())
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.875rem; box-shadow: 0 2px 8px rgba(0, 168, 255, 0.3);">
                                            {{ substr($message->sender->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="p-3 rounded-4 {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white' }}" style="box-shadow: 0 4px 16px rgba(0,0,0,0.12); position: relative;">
                                        <p class="mb-2" style="word-wrap: break-word; line-height: 1.5; margin: 0;">{{ $message->message }}</p>
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.75rem;">
                                                {{ $message->created_at->format('H:i') }}
                                            </small>
                                            @if($message->sender_id === auth()->id())
                                                @if($message->isRead())
                                                    <i class="bi bi-check2-all text-white-50" style="font-size: 0.875rem;"></i>
                                                @else
                                                    <i class="bi bi-check2 text-white-50" style="font-size: 0.875rem;"></i>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @if($message->sender_id === auth()->id())
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.875rem; box-shadow: 0 2px 8px rgba(0, 168, 255, 0.3);">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-chat-dots text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                        <p class="text-muted mb-0">Nessun messaggio ancora. Inizia la conversazione!</p>
                    </div>
                @endif
            </div>

            <!-- Form invio messaggio -->
            <div class="border-top p-4 bg-white" style="border-top: 1px solid rgba(0, 0, 0, 0.08) !important;">
                <form action="{{ route('messages.store', $user) }}" method="POST" id="message-form" class="d-flex gap-2">
                    @csrf
                    <div class="flex-grow-1 position-relative">
                        <input type="text" name="message" class="form-control" placeholder="Scrivi un messaggio..." required maxlength="1000" autofocus style="border-radius: 24px; padding: 0.75rem 1.5rem; border: 2px solid #e5e5e7; transition: all 0.2s ease;">
                        <div class="position-absolute top-50 end-0 translate-middle-y pe-3" style="pointer-events: none;">
                            <small class="text-muted" id="char-count" style="font-size: 0.75rem;">0/1000</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-cta" style="border-radius: 24px; padding: 0.75rem 2rem; min-width: 120px; box-shadow: 0 4px 16px rgba(0, 168, 255, 0.3);">
                        <i class="bi bi-send-fill me-2"></i> Invia
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>

<style>
#messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: transparent;
}

#messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0, 0, 0, 0.3);
}

#message-form input:focus {
    border-color: #00A8FF !important;
    box-shadow: 0 0 0 4px rgba(0, 168, 255, 0.15) !important;
    outline: none;
}

#message-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 168, 255, 0.4) !important;
}

#message-form button:active {
    transform: translateY(0);
}
</style>

<script>
// Scroll automatico alla fine dei messaggi
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Contatore caratteri
    const messageInput = document.querySelector('#message-form input[name="message"]');
    const charCount = document.getElementById('char-count');
    
    if (messageInput && charCount) {
        messageInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length + '/1000';
            
            if (length > 900) {
                charCount.style.color = '#dc3545';
            } else if (length > 700) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
            }
        });
    }
});
</script>
