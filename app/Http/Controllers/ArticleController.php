<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Article::class, 'article');
    }

    public function index(): View
    {
        $articlesQuery = Article::query()->with(['author', 'tags']);

        if (auth()->check() && auth()->user()->isAdmin()) {
            // Gli admin vedono tutti gli articoli
            $articles = $articlesQuery
                ->latestFirst()
                ->paginate(9)
                ->withQueryString();
        } else {
            $articles = $articlesQuery
                ->when(auth()->check(), function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->published()
                            ->orWhere('user_id', auth()->id());
                    });
                }, fn ($query) => $query->published())
                ->latestFirst()
                ->paginate(9)
                ->withQueryString();
        }

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        $article = new Article();

        return view('articles.create', [
            'article' => $article,
            'tagList' => '',
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $article = $request->user()->articles()->create([
                'title' => $data['title'],
                'slug' => $this->uniqueSlug($data['title']),
                'excerpt' => $data['excerpt'],
                'body' => $data['body'],
                'published_at' => $request->resolvePublishedAt(),
                'is_anonymous' => $request->boolean('is_anonymous', false),
            ]);

            if ($request->hasFile('cover_image')) {
                try {
                    $path = $request->file('cover_image')->store('articles', 'public');
                    $article->update([
                        'cover_image' => $path,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Errore upload immagine: ' . $e->getMessage());
                    // Continua senza l'immagine se c'è un errore
                }
            }

            $article->syncTags($request->tags());

            return redirect()
                ->route('articles.show', $article)
                ->with('status', 'Storia creata con successo.');
        } catch (\Exception $e) {
            \Log::error('Errore nella creazione articolo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', '_token']),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Si è verificato un errore durante la creazione della storia. Riprova più tardi.']);
        }
    }

    public function show(Article $article): View
    {
        $article->loadMissing('author', 'tags');

        abort_unless(
            (auth()->check() && auth()->user()->isAdmin())
            || $article->isPublished()
            || auth()->id() === $article->user_id,
            404
        );
        
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        return view('articles.edit', [
            'article' => $article,
            'tagList' => $article->tags->pluck('name')->implode(', '),
        ]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();

        $attributes = [
            'title' => $data['title'],
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'published_at' => $request->resolvePublishedAt($article->published_at),
            'is_anonymous' => $request->boolean('is_anonymous', false),
        ];

        if ($article->title !== $data['title']) {
            $attributes['slug'] = $this->uniqueSlug($data['title'], $article);
        }

        if ($request->hasFile('cover_image')) {
            try {
                if ($article->cover_image) {
                    Storage::disk('public')->delete($article->cover_image);
                }

                $attributes['cover_image'] = $request->file('cover_image')->store('articles', 'public');
            } catch (\Exception $e) {
                \Log::error('Errore upload immagine durante aggiornamento: ' . $e->getMessage());
                // Non aggiornare l'immagine se c'è un errore
            }
        }

        $article->update($attributes);
        $article->syncTags($request->tags());

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Articolo aggiornato con successo.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }
        
        $article->delete();
        
        return redirect()
            ->route('articles.index')
            ->with('status', 'Articolo eliminato.');
    }

    protected function uniqueSlug(string $title, ?Article $ignore = null): string
    {
        $baseSlug = Str::slug($title) ?: Str::random(8);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Article::where('slug', $slug)
                ->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
