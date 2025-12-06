@php
    use Illuminate\Support\Facades\Cookie;
@endphp
@if(!Cookie::has('cookie_consent'))
<div id="cookieBanner" class="cookie-banner" style="display: none;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <h6 class="fw-semibold mb-2">Utilizziamo i cookie</h6>
                <p class="mb-0 small">Questo sito utilizza cookie tecnici necessari per il funzionamento e cookie di analisi per migliorare l'esperienza utente. Puoi gestire le tue preferenze in qualsiasi momento.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                    <button type="button" class="btn btn-outline-light btn-sm" id="cookieReject" style="border-radius: 24px; padding: 0.5rem 1.5rem;">
                        Rifiuta
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="cookieAccept" style="border-radius: 24px; padding: 0.5rem 1.5rem;">
                        Accetta tutti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

