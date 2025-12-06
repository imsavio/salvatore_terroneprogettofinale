<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();
        
        // Ottieni tutte le conversazioni (amici con cui hai scambiato messaggi)
        $conversations = DB::table('messages')
            ->select(DB::raw('
                CASE 
                    WHEN sender_id = ' . auth()->id() . ' THEN receiver_id
                    ELSE sender_id
                END as other_user_id,
                MAX(created_at) as last_message_at
            '))
            ->where(function ($query) {
                $query->where('sender_id', auth()->id())
                      ->orWhere('receiver_id', auth()->id());
            })
            ->groupBy('other_user_id')
            ->orderByDesc('last_message_at')
            ->get();

        $conversationUsers = collect();
        foreach ($conversations as $conv) {
            $otherUser = User::find($conv->other_user_id);
            if ($otherUser) {
                // Verifica che siano amici (in entrambe le direzioni)
                $isFriend = DB::table('friendships')
                    ->where('status', 'accepted')
                    ->where(function ($q) use ($otherUser) {
                        $q->where('user_id', auth()->id())
                          ->where('friend_id', $otherUser->id);
                    })
                    ->orWhere(function ($q) use ($otherUser) {
                        $q->where('user_id', $otherUser->id)
                          ->where('friend_id', auth()->id());
                    })
                    ->exists();
                
                if (!$isFriend) {
                    continue; // Salta se non sono amici
                }
                
                $lastMessage = Message::with(['sender', 'receiver'])
                    ->where(function ($q) use ($otherUser) {
                        $q->where('sender_id', auth()->id())
                          ->where('receiver_id', $otherUser->id);
                    })->orWhere(function ($q) use ($otherUser) {
                        $q->where('sender_id', $otherUser->id)
                          ->where('receiver_id', auth()->id());
                    })->latest()->first();

                $unreadCount = Message::where('sender_id', $otherUser->id)
                    ->where('receiver_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();

                $conversationUsers->push([
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ]);
            }
        }

        return view('messages.index', compact('conversationUsers'));
    }

    public function show(User $user): View
    {
        // Verifica che siano amici (in entrambe le direzioni)
        $isFriend = DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('user_id', auth()->id())
                  ->where('friend_id', $user->id);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', auth()->id());
            })
            ->exists();
        
        if (!$isFriend) {
            return redirect()->route('friends.index')->with('error', 'Puoi chattare solo con i tuoi amici.');
        }

        // Segna i messaggi come letti
        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();

        return view('messages.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        // Verifica che siano amici (in entrambe le direzioni)
        $isFriend = \DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('user_id', auth()->id())
                  ->where('friend_id', $user->id);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', auth()->id());
            })
            ->exists();
        
        if (!$isFriend) {
            return back()->with('error', 'Puoi chattare solo con i tuoi amici.');
        }

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        return back()->with('status', 'Messaggio inviato.');
    }
}
