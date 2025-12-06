<x-app-layout>
    <section class="container py-5">
        <div class="row gy-5 align-items-start">
            <div class="col-lg-5">
                <span class="badge badge-soft">Parliamone</span>
                <h1 class="fw-semibold mt-3 mb-3">Contattaci</h1>
                <p class="text-muted">Hai bisogno di supporto, vuoi collaborare o proporre un articolo? Scrivici e ti risponderemo al più presto.</p>

                <div class="card card-modern p-4 mt-4">
                    <h5 class="fw-semibold mb-3">Info utili</h5>
                    <p class="mb-2"><i class="bi bi-envelope-open me-2 text-primary"></i>{{ config('mail.from.address') }}</p>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Milano · Remote Friendly</p>
                </div>
            </div>
            <div class="col-lg-7">
                @if(session('status'))
                    <div class="alert alert-modern alert-success">{{ session('status') }}</div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="card card-modern p-4 shadow-sm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="subject" class="form-label">Oggetto</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="es. Proposta di collaborazione">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Messaggio</label>
                            <textarea name="message" id="message" rows="6" class="form-control @error('message') is-invalid @enderror" placeholder="Raccontaci in dettaglio la tua richiesta" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="text-muted mb-0 small">Rispondiamo entro 24 ore lavorative.</p>
                        <button type="submit" class="btn btn-primary btn-cta">Invia messaggio</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>

