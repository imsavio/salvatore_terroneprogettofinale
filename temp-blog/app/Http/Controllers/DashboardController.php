<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $articleCount = $user->articles()->count();
        $publishedCount = $user->articles()->published()->count();
        $nextArticle = $user->articles()
            ->whereNull('published_at')
            ->latest('updated_at')
            ->first();
        $latestArticles = $user->articles()
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'articleCount',
            'publishedCount',
            'nextArticle',
            'latestArticles'
        ));
    }
}
