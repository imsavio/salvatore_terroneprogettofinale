<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('stores contact messages and sends a notification email', function () {
    Mail::fake();

    $response = $this->post(route('contact.store'), [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'subject' => 'Richiesta informazioni',
        'message' => str_repeat('Messaggio molto interessante. ', 4),
    ]);

    $response->assertSessionHas('status');
    $this->assertDatabaseHas('contact_messages', [
        'email' => 'mario.rossi@example.com',
        'subject' => 'Richiesta informazioni',
    ]);

    Mail::assertSentCount(1);
});
