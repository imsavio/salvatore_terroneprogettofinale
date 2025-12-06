<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\FriendRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FriendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();
        
        // Ottieni amici in entrambe le direzioni
        $friendsAsUser = $user->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->get();
        
        $friendsAsFriend = $user->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->get();
        
        $allFriends = $friendsAsUser->merge($friendsAsFriend)->unique('id');
        
        // Pagina manualmente
        $currentPage = request()->get('page', 1);
        $perPage = 12;
        $items = $allFriends->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $friends = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allFriends->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        $friendRequests = $user->friendRequests()->paginate(10);
        $sentRequests = $user->sentFriendRequests()->paginate(10);

        return view('friends.index', compact('friends', 'friendRequests', 'sentRequests'));
    }

    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $users = collect();

        if ($query) {
            $users = User::where('id', '!=', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('public_id', 'like', "%{$query}%");
                })
                ->paginate(12);
        }

        return view('friends.search', compact('users', 'query'));
    }

    public function add(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Non puoi aggiungere te stesso come amico.');
        }

        // Verifica se esiste già una richiesta
        $existing = DB::table('friendships')
            ->where(function ($q) use ($user) {
                $q->where('user_id', auth()->id())
                  ->where('friend_id', $user->id);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', auth()->id());
            })
            ->first();

        if ($existing) {
            return back()->with('error', 'Richiesta di amicizia già esistente.');
        }

        DB::table('friendships')->insert([
            'user_id' => auth()->id(),
            'friend_id' => $user->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Invia notifica all'utente che riceve la richiesta
        $user->notify(new FriendRequestReceived(auth()->user()));

        return back()->with('status', 'Richiesta di amicizia inviata.');
    }

    public function accept(Request $request, User $user): RedirectResponse
    {
        DB::table('friendships')
            ->where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->update(['status' => 'accepted', 'updated_at' => now()]);

        // Segna le notifiche relative come lette
        auth()->user()->notifications()
            ->where('type', 'App\Notifications\FriendRequestReceived')
            ->whereJsonContains('data->sender_id', $user->id)
            ->update(['read_at' => now()]);

        return back()->with('status', 'Richiesta di amicizia accettata.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        DB::table('friendships')
            ->where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->delete();

        // Segna le notifiche relative come lette
        auth()->user()->notifications()
            ->where('type', 'App\Notifications\FriendRequestReceived')
            ->whereJsonContains('data->sender_id', $user->id)
            ->update(['read_at' => now()]);

        return back()->with('status', 'Richiesta di amicizia rifiutata.');
    }

    public function remove(Request $request, User $user): RedirectResponse
    {
        DB::table('friendships')
            ->where(function ($q) use ($user) {
                $q->where('user_id', auth()->id())
                  ->where('friend_id', $user->id);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', auth()->id());
            })
            ->delete();

        return back()->with('status', 'Amicizia rimossa.');
    }
}
