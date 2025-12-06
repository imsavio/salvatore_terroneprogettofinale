<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuovo messaggio dal sito',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.received',
            with: [
                'messageData' => $this->message,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
