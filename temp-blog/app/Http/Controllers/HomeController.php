<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredArticles = Article::published()
            ->latestFirst()
            ->take(3)
            ->get();

        $metrics = [
            'articles' => Article::published()->where('published_at', '>=', now()->subMonth())->count(),
            'authors' => User::whereHas('articles', fn ($query) => $query->published())->count(),
            'topTags' => Tag::whereHas('articles', fn ($query) => $query->published())
                ->withCount(['articles as articles_count' => fn ($query) => $query->published()])
                ->orderByDesc('articles_count')
                ->limit(5)
                ->pluck('name')
                ->toArray(),
        ];

        return view('home', compact('featuredArticles', 'metrics'));
    }
}
