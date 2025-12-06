<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Tutte le notifiche sono state segnate come lette.');
    }

    public function destroy(string $id): RedirectResponse
    {
        auth()->user()->notifications()->find($id)?->delete();

        return back()->with('status', 'Notifica eliminata.');
    }
}
