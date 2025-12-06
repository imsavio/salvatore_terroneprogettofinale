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
        try {
            // Salva il messaggio nel database
            $message = ContactMessage::create($request->validated());
            
            // Prova a inviare l'email, ma non fallire se non funziona
            try {
                $recipient = config('mail.from.address', 'agenziaaulab@mail.com');
                Mail::to($recipient)->send(new ContactMessageReceived($message));
            } catch (\Exception $emailException) {
                // Log dell'errore email ma continua
                \Log::warning('Errore invio email contatto: ' . $emailException->getMessage(), [
                    'message_id' => $message->id,
                    'exception' => $emailException->getTraceAsString()
                ]);
            }
                
            return back()->with('status', 'Grazie per averci contattato! Ti risponderemo a breve.');
        } catch (\Exception $e) {
            \Log::error('Errore salvataggio messaggio contatto: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Si è verificato un errore durante l\'invio del messaggio. Riprova più tardi.']);
        }
    }
}
