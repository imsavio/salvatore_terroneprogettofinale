<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = [
            'users' => User::count(),
            'articles' => Article::count(),
            'publishedArticles' => Article::published()->count(),
            'tags' => Tag::count(),
        ];

        $latestUsers = User::latest()->limit(5)->get();
        $latestArticles = Article::latestFirst()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestArticles'));
    }
}


