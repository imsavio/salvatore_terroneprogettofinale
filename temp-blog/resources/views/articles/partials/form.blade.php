@csrf
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
                <label for="body" class="form-label">Contenuto</label>
                <textarea name="body" id="body" rows="12" class="form-control @error('body') is-invalid @enderror" placeholder="Scrivi qui il tuo articolo..." required>{{ old('body', $article->body) }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Suggerimento: utilizza paragrafi brevi e suddividi il contenuto con titoli H2/H3.</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-modern p-4 mb-4">
            <h5 class="fw-semibold mb-3">Dettagli pubblicazione</h5>

            <div class="mb-3">
                <label class="form-label">Stato</label>
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
                <small class="text-muted">Scegli "Pubblicato" per renderlo visibile pubblicamente.</small>
            </div>

            <div class="mb-3">
                <label for="published_at" class="form-label">Data di pubblicazione</label>
                <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Lascia vuoto per pubblicare immediatamente.</small>
            </div>

            <div class="mb-3">
                <label for="tags" class="form-label">Tag</label>
                <input type="text" name="tags" id="tags" class="form-control @error('tags') is-invalid @enderror" value="{{ old('tags', $tagList) }}" placeholder="es. tecnologia, design, innovazione">
                @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Separare i tag con virgola o punto e virgola.</small>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Immagine di copertina</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Formato consigliato 1200x640px. Max 4MB.</small>
            </div>

            @if($article->cover_image)
                <div class="mb-3">
                    <span class="text-muted d-block mb-2">Anteprima attuale</span>
                    <img src="{{ asset('storage/'.$article->cover_image) }}" alt="Cover attuale" class="img-fluid rounded-4">
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100 btn-cta">{{ $submitLabel }}</button>
        </div>

        @if($article->exists)
            <div class="card card-modern p-4">
                <h6 class="text-uppercase text-muted mb-3">Azioni rapide</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-secondary flex-fill">Vedi anteprima</a>
                    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary flex-fill">Torna alla lista</a>
                </div>
            </div>
        @endif
    </div>
</div>

