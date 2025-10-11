<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Tag;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with(['user', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(10);

        $topTags = Tag::withCount('articles')
            ->orderByDesc('articles_count')
            ->limit(15)
            ->get();

        return view('homepage', compact('articles', 'topTags'));
    }
}
