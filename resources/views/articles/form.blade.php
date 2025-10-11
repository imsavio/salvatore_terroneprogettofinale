@props(['article' => null, 'action' => '', 'method' => 'POST'])

<form id="article-form" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        {{ $article ? 'Modifica Articolo' : 'Nuovo Articolo' }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">
                            Titolo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $article?->title) }}"
                               placeholder="Inserisci il titolo dell'articolo..."
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Un titolo accattivante aiuta a catturare l'attenzione dei lettori</div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">
                            Contenuto <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="15"
                                  placeholder="Scrivi il contenuto del tuo articolo...">{{ old('content', $article?->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Usa l'editor per formattare il testo, aggiungere link e immagini</div>
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-4">
                        <label for="excerpt" class="form-label fw-semibold">
                            Estratto
                        </label>
                        <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                  id="excerpt" 
                                  name="excerpt" 
                                  rows="3"
                                  placeholder="Breve descrizione dell'articolo (opzionale)...">{{ old('excerpt', $article?->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Se lasciato vuoto, verrà generato automaticamente dal contenuto</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Publish Options -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2 text-primary"></i>Opzioni
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Published Date -->
                    <div class="mb-4">
                        <label for="published_at" class="form-label fw-semibold">
                            Data Pubblicazione
                        </label>
                        <input type="datetime-local" 
                               class="form-control @error('published_at') is-invalid @enderror" 
                               id="published_at" 
                               name="published_at" 
                               value="{{ old('published_at', $article?->published_at?->format('Y-m-d\TH:i')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Lascia vuoto per salvare come bozza</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="publish" class="btn btn-primary">
                            <i class="fas fa-globe me-2"></i>
                            {{ $article ? 'Aggiorna e Pubblica' : 'Pubblica' }}
                        </button>
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                            <i class="fas fa-save me-2"></i>
                            Salva come Bozza
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Annulla
                        </a>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2 text-primary"></i>Immagine Featured
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Current Image -->
                    @if($article?->featured_image)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Immagine Attuale</label>
                            <div id="current-image-preview">
                                <img src="{{ $article->featured_image }}" 
                                     class="img-fluid rounded" 
                                     alt="Current image">
                            </div>
                        </div>
                    @endif

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Carica Nuova Immagine</label>
                        <div id="image-upload" class="dropzone border-2 border-dashed rounded p-4 text-center">
                            <div class="dz-message">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Trascina qui l'immagine o clicca per selezionare</p>
                            </div>
                        </div>
                        <input type="hidden" name="featured_image" value="{{ old('featured_image', $article?->featured_image) }}">
                        @error('featured_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div id="image-preview" class="mt-3" style="display: none;">
                        <label class="form-label fw-semibold">Anteprima</label>
                        <div class="border rounded p-2">
                            <!-- Preview will be inserted here by JavaScript -->
                        </div>
                    </div>

                    <!-- Alternative: File Input -->
                    <div class="mt-3">
                        <label for="image_file" class="form-label fw-semibold">Oppure seleziona file</label>
                        <input type="file" 
                               class="form-control @error('image_file') is-invalid @enderror" 
                               id="image_file" 
                               name="image_file" 
                               accept="image/*"
                               onchange="previewImage(this)">
                        @error('image_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>Tag
                    </h5>
                </div>
                <div class="card-body">
                    <select class="form-select @error('tags') is-invalid @enderror" 
                            id="tags" 
                            name="tags[]" 
                            multiple>
                        @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
                            <option value="{{ $tag->id }}" 
                                    {{ in_array($tag->id, old('tags', $article?->tags->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}
                                    style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tags')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Seleziona i tag che meglio descrivono il contenuto</div>
                </div>
            </div>

            <!-- Article Stats (Edit only) -->
            @if($article)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>Statistiche
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="h5 text-primary mb-1">{{ $article->tags->count() }}</div>
                                <small class="text-muted">Tag</small>
                            </div>
                            <div class="col-6">
                                <div class="h5 text-success mb-1">{{ str_word_count($article->content) }}</div>
                                <small class="text-muted">Parole</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i>
                                Creato: {{ $article->created_at->format('d M Y') }}
                            </small>
                        </div>
                        @if($article->updated_at != $article->created_at)
                            <div>
                                <small class="text-muted">
                                    <i class="far fa-edit me-1"></i>
                                    Modificato: {{ $article->updated_at->format('d M Y') }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</form>

<!-- Loading Overlay -->
<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Caricamento...</span>
            </div>
            <h5>Salvataggio in corso...</h5>
            <p class="mb-0">Attendere prego</p>
        </div>
    </div>
</div>

<style>
/* Form improvements */
.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Dropzone styling */
.dropzone {
    border: 2px dashed #dee2e6 !important;
    border-radius: 0.5rem;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.dropzone:hover {
    border-color: #0d6efd !important;
    background: #e7f1ff;
}

.dropzone.dz-drag-hover {
    border-color: #0d6efd !important;
    background: #e7f1ff;
}

/* Select2 customization */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
}

.select2-container--default .select2-selection--multiple:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* TinyMCE customization */
.tox-tinymce {
    border-radius: 0.375rem;
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Image preview */
#image-preview img {
    max-height: 200px;
    object-fit: cover;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

