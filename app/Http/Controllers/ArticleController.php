<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use App\Http\Requests\StoreArticleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with(['user:id,name,username', 'tags:id,name,slug,color'])->published();

        // Search functionality with full-text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by tag with optimized query
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by author with optimized query
        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        // Filter by date range with index optimization
        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        // Sorting with proper indexing
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'author':
                $query->join('users', 'articles.user_id', '=', 'users.id')
                      ->orderBy('users.name')
                      ->select('articles.*');
                break;
            default:
                $query->latest('published_at');
        }

        $articles = $query->paginate(12)->withQueryString();

        // Get filter options with caching
        $tags = cache()->remember('tags_with_count', 3600, function () {
            return Tag::withCount('articles')->orderBy('articles_count', 'desc')->get();
        });
        
        $authors = cache()->remember('authors_with_count', 3600, function () {
            return \App\Models\User::withCount('articles')->having('articles_count', '>', 0)->get();
        });

        return view('articles.index', compact('articles', 'tags', 'authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::all();
        return view('articles.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        // Handle published_at based on action
        $publishedAt = $request->published_at;
        if ($request->input('action') === 'draft') {
            $publishedAt = null;
        } elseif (empty($publishedAt)) {
            $publishedAt = now();
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'published_at' => $publishedAt,
            'user_id' => Auth::id(),
        ]);

        // Handle tags - create new ones if they don't exist
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagInput) {
                if (is_numeric($tagInput)) {
                    // Existing tag ID
                    $tagIds[] = $tagInput;
                } else {
                    // New tag name - create it
                    $tag = \App\Models\Tag::firstOrCreate(
                        ['name' => $tagInput],
                        [
                            'slug' => \Str::slug($tagInput),
                            'color' => $this->generateRandomColor()
                        ]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $article->tags()->sync($tagIds);
        }

        $message = $request->input('action') === 'draft' 
            ? 'Articolo salvato come bozza con successo!' 
            : 'Articolo pubblicato con successo!';

        return redirect()->route('articles.show', $article)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->load(['user', 'tags']);
        
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        Gate::authorize('update', $article);
        
        $tags = Tag::all();
        $selectedTags = $article->tags->pluck('id')->toArray();
        
        return view('articles.edit', compact('article', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreArticleRequest $request, Article $article)
    {
        Gate::authorize('update', $article);

        // Handle published_at based on action
        $publishedAt = $request->published_at;
        if ($request->input('action') === 'draft') {
            $publishedAt = null;
        } elseif (empty($publishedAt) && $request->input('action') === 'publish') {
            $publishedAt = now();
        }

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'published_at' => $publishedAt,
        ]);

        // Handle tags - create new ones if they don't exist
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagInput) {
                if (is_numeric($tagInput)) {
                    // Existing tag ID
                    $tagIds[] = $tagInput;
                } else {
                    // New tag name - create it
                    $tag = \App\Models\Tag::firstOrCreate(
                        ['name' => $tagInput],
                        [
                            'slug' => \Str::slug($tagInput),
                            'color' => $this->generateRandomColor()
                        ]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $article->tags()->sync($tagIds);
        } else {
            $article->tags()->detach();
        }

        $message = $request->input('action') === 'draft' 
            ? 'Articolo salvato come bozza con successo!' 
            : 'Articolo aggiornato con successo!';

        return redirect()->route('articles.show', $article)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        Gate::authorize('delete', $article);
        
        $article->delete();
        
        return redirect()->route('home')
            ->with('success', 'Articolo eliminato con successo!');
    }

    /**
     * Generate a random color for tags.
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
            '#14B8A6', '#F43F5E', '#8B5A2B', '#059669', '#DC2626'
        ];

        return $colors[array_rand($colors)];
    }
}
