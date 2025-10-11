<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * Display a listing of all tags.
     */
    public function index()
    {
        $tags = Tag::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Display articles for a specific tag.
     */
    public function show(Tag $tag)
    {
        $articles = Article::with(['user', 'tags'])
            ->whereHas('tags', function($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->published()
            ->latest('published_at')
            ->paginate(12);

        return view('tags.show', compact('tag', 'articles'));
    }

    /**
     * API endpoint for tag autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $tags = Tag::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'color']);

        return response()->json($tags);
    }

    /**
     * API endpoint to create a new tag.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'color' => $this->generateRandomColor()
        ]);

        return response()->json([
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'color' => $tag->color
        ]);
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

    /**
     * Get popular tags for sidebar.
     */
    public function popular()
    {
        return Tag::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('articles_count', 'desc')
            ->limit(10)
            ->get();
    }
}

