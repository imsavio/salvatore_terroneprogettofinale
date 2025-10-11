<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display the current user's profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $user->loadCount(['articles' => function($query) {
            $query->published();
        }]);

        // Get user's articles with pagination
        $articles = Article::where('user_id', $user->id)
            ->published()
            ->with(['tags'])
            ->latest('published_at')
            ->paginate(6);

        // Get user's most used tags
        $popularTags = Tag::whereHas('articles', function($query) use ($user) {
            $query->where('user_id', $user->id)->published();
        })
        ->withCount(['articles' => function($query) use ($user) {
            $query->where('user_id', $user->id)->published();
        }])
        ->orderBy('articles_count', 'desc')
        ->limit(10)
        ->get();

        // Get article statistics for chart
        $articleStats = $this->getArticleStats($user);

        return view('users.profile', compact('user', 'articles', 'popularTags', 'articleStats'));
    }

    /**
     * Display a public user profile.
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $user->loadCount(['articles' => function($query) {
            $query->published();
        }]);

        // Get user's articles with pagination
        $articles = Article::where('user_id', $user->id)
            ->published()
            ->with(['tags'])
            ->latest('published_at')
            ->paginate(6);

        // Get user's most used tags
        $popularTags = Tag::whereHas('articles', function($query) use ($user) {
            $query->where('user_id', $user->id)->published();
        })
        ->withCount(['articles' => function($query) use ($user) {
            $query->where('user_id', $user->id)->published();
        }])
        ->orderBy('articles_count', 'desc')
        ->limit(10)
        ->get();

        // Get article statistics for chart
        $articleStats = $this->getArticleStats($user);

        return view('users.show', compact('user', 'articles', 'popularTags', 'articleStats'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Handle password change
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La password attuale non è corretta.']);
            }
            $user->password = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'bio' => $request->bio,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Profilo aggiornato con successo!');
    }

    /**
     * Get article statistics for the user.
     */
    private function getArticleStats(User $user)
    {
        $articles = Article::where('user_id', $user->id)
            ->published()
            ->selectRaw('strftime("%Y-%m", published_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $stats = [];
        $labels = [];
        $data = [];

        // Generate last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthName = now()->subMonths($i)->format('M Y');
            
            $labels[] = $monthName;
            $data[] = $articles->where('month', $month)->first()->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}

