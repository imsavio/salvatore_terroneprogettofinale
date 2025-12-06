<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::create($request->validated());
        $recipient = config('mail.from.address', 'team@novablog.test');

        Mail::to($recipient)->send(new ContactMessageReceived($message));

        return back()->with('status', 'Grazie per averci contattato! Ti risponderemo a breve.');
    }
}
