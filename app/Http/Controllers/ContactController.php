<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Send contact form email.
     */
    public function send(Request $request)
    {
        // Rate limiting - 5 emails per minute per IP
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Troppi tentativi. Riprova tra {$seconds} secondi."
            ])->withInput();
        }

        // Honeypot protection
        if ($request->filled('website')) {
            return back()->with('success', 'Messaggio inviato con successo!');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
            'website' => 'nullable|string', // Honeypot
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'name.max' => 'Il nome non può superare i 255 caratteri.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'email.max' => 'L\'email non può superare i 255 caratteri.',
            'subject.required' => 'Il soggetto è obbligatorio.',
            'subject.max' => 'Il soggetto non può superare i 255 caratteri.',
            'message.required' => 'Il messaggio è obbligatorio.',
            'message.min' => 'Il messaggio deve contenere almeno 10 caratteri.',
            'message.max' => 'Il messaggio non può superare i 2000 caratteri.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Additional email validation
        if (!$this->isValidEmail($request->email)) {
            return back()->withErrors([
                'email' => 'L\'indirizzo email non è valido.'
            ])->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ];

        try {
            // Send confirmation email to user
            Mail::to($request->email)->send(new ContactConfirmation($data));

            // Send notification email to admin
            Mail::to(config('mail.admin_email', 'admin@example.com'))->send(new ContactNotification($data));

            // Increment rate limiter
            RateLimiter::hit($key, 60); // 1 minute

            return back()->with('success', 'Messaggio inviato con successo! Ti risponderemo presto.');

        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Si è verificato un errore durante l\'invio. Riprova più tardi.'
            ])->withInput();
        }
    }

    /**
     * Advanced email validation.
     */
    private function isValidEmail($email)
    {
        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check for disposable email domains
        $disposableDomains = [
            '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
            'mailinator.com', 'throwaway.email', 'temp-mail.org'
        ];

        $domain = substr(strrchr($email, "@"), 1);
        if (in_array($domain, $disposableDomains)) {
            return false;
        }

        // Check MX record
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }

        return true;
    }
}