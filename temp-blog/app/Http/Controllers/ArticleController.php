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
        $articles = Article::query()
            ->with(['author', 'tags'])
            ->when(auth()->check(), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->published()
                        ->orWhere('user_id', auth()->id());
                });
            }, fn ($query) => $query->published())
            ->latestFirst()
            ->paginate(9)
            ->withQueryString();

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
        $data = $request->validated();

        $article = $request->user()->articles()->create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'published_at' => $request->resolvePublishedAt(),
        ]);

        if ($request->hasFile('cover_image')) {
            $article->update([
                'cover_image' => $request->file('cover_image')->store('articles', 'public'),
            ]);
        }

        $article->syncTags($request->tags());

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Articolo creato con successo.');
    }

    public function show(Article $article): View
    {
        $article->loadMissing('author', 'tags');

        abort_unless($article->isPublished() || auth()->id() === $article->user_id, 404);

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
        ];

        if ($article->title !== $data['title']) {
            $attributes['slug'] = $this->uniqueSlug($data['title'], $article);
        }

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $attributes['cover_image'] = $request->file('cover_image')->store('articles', 'public');
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
