@if($article->exists)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-modern p-4">
            <div class="mb-3">
                <label for="title" class="form-label">Titolo</label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="excerpt" class="form-label">Descrizione breve</label>
                <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" required>{{ old('excerpt', $article->excerpt) }}</textarea>
                @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="body" class="form-label">Contenuto <span class="text-danger">*</span></label>
                <textarea name="body" id="body" rows="12" class="form-control @error('body') is-invalid @enderror" placeholder="Scrivi qui la tua storia..." required minlength="120">{{ old('body', $article->body) }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="mt-2 d-flex align-items-center gap-2">
                    <small class="text-muted">Caratteri: <span id="body-char-count">0</span>/120 minimo</small>
                    <div class="info-tooltip-wrapper">
                        <i class="bi bi-info-circle text-primary" style="cursor: help; font-size: 1rem;"></i>
                        <div class="info-tooltip">Minimo 120 caratteri. Suggerimento: utilizza paragrafi brevi e racconta la tua storia in modo dettagliato.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-modern p-4 mb-4">
            <h5 class="fw-semibold mb-3">Dettagli pubblicazione</h5>

            <div class="mb-3">
                <label class="form-label d-flex align-items-center gap-2">
                    Stato
                    <div class="info-tooltip-wrapper">
                        <i class="bi bi-info-circle text-primary" style="cursor: help; font-size: 0.875rem;"></i>
                        <div class="info-tooltip">Scegli "Pubblicato" per renderlo visibile pubblicamente. La pubblicazione avverrà immediatamente.</div>
                    </div>
                </label>
                @php
                    $status = old('status', $article->isPublished() ? 'published' : 'draft');
                @endphp
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="draft" @selected($status === 'draft')>Bozza</option>
                    <option value="published" @selected($status === 'published')>Pubblicato</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tags" class="form-label d-flex align-items-center gap-2">
                    Tag
                    <div class="info-tooltip-wrapper">
                        <i class="bi bi-info-circle text-primary" style="cursor: help; font-size: 0.875rem;"></i>
                        <div class="info-tooltip">Separare i tag con virgola o punto e virgola.</div>
                    </div>
                </label>
                <input type="text" name="tags" id="tags" class="form-control @error('tags') is-invalid @enderror" value="{{ old('tags', $tagList) }}" placeholder="es. tecnologia, design, innovazione">
                @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_anonymous" id="is_anonymous" value="1" {{ old('is_anonymous', $article->is_anonymous ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_anonymous">
                        Pubblica in modo anonimo
                    </label>
                    <div class="info-tooltip-wrapper">
                        <i class="bi bi-info-circle text-primary" style="cursor: help; font-size: 0.875rem;"></i>
                        <div class="info-tooltip">Se selezionato, il tuo nome non verrà mostrato pubblicamente.</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label d-flex align-items-center gap-2">
                    Immagine di copertina
                    <div class="info-tooltip-wrapper">
                        <i class="bi bi-info-circle text-primary" style="cursor: help; font-size: 0.875rem;"></i>
                        <div class="info-tooltip">Formato consigliato 1200x640px. Max 4MB.</div>
                    </div>
                </label>
                <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($article->cover_image)
                <div class="mb-3">
                    <span class="text-muted d-block mb-2">Anteprima attuale</span>
                    <img src="{{ asset('storage/'.$article->cover_image) }}" alt="Cover attuale" class="img-fluid rounded-4">
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100 btn-cta">{{ $submitLabel }}</button>
        </div>
    </div>
</div>

