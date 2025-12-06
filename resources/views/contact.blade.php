<x-app-layout>
    <section class="container py-5">
        <div class="row gy-4 align-items-start">
            <div class="col-lg-6">
                <h1 class="fw-semibold mt-3 mb-3">Contattaci</h1>
                <p class="text-muted mb-4">Hai bisogno di supporto, vuoi collaborare o proporre un articolo? Scrivici e ti risponderemo al più presto.</p>

                <div class="card card-modern p-4 mb-4">
                    <h5 class="fw-semibold mb-3">Info utili</h5>
                    <p class="mb-2"><i class="bi bi-envelope-open me-2 text-primary"></i>agenziaaulab@mail.com</p>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Duomo di Milano, Milano</p>
                </div>
                
                <div class="card card-modern p-4">
                    <h5 class="fw-semibold mb-3">La nostra sede</h5>
                    <div class="map-container" style="height: 300px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); position: relative;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2798.0!2d9.1919!3d45.4642!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4786c1493f1275e1%3A0x3f0a4b5e5e5e5e5e!2sPiazza%20del%20Duomo!5e0!3m2!1sit!2sit!4v1234567890" 
                            width="100%" 
                            height="100%" 
                            style="border:0; border-radius: 12px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Mappa - Duomo di Milano">
                        </iframe>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="https://www.google.com/maps/search/?api=1&query=45.4642,9.1919" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-geo-alt me-1"></i> Apri in Google Maps
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <h5 class="fw-semibold mb-2">Si sono verificati i seguenti errori:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="card card-modern p-5 shadow-sm">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nome</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="subject" class="form-label fw-semibold">Oggetto</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="es. Proposta di collaborazione">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label fw-semibold">Messaggio</label>
                            <textarea name="message" id="message" rows="7" class="form-control @error('message') is-invalid @enderror" placeholder="Raccontaci in dettaglio la tua richiesta" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-5 gap-3">
                        <p class="text-muted mb-0 small">Rispondiamo entro 24 ore lavorative.</p>
                        <button type="submit" class="btn btn-primary btn-cta w-100 w-sm-auto">Invia messaggio</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>




