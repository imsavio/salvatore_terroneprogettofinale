<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;

class ContactTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_display_contact_form()
    {
        $response = $this->get(route('contact'));
        
        $response->assertStatus(200);
        $response->assertViewIs('contact');
        $response->assertSee('Contattaci');
    }

    /** @test */
    public function it_can_send_contact_message()
    {
        Mail::fake();

        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.'
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(ContactConfirmation::class);
        Mail::assertSent(ContactNotification::class);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post(route('contact.send'), []);
        
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.'
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_validates_message_minimum_length()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Short'
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertSessionHasErrors(['message']);
    }

    /** @test */
    public function it_validates_message_maximum_length()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => str_repeat('a', 2001) // More than 2000 characters
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertSessionHasErrors(['message']);
    }

    /** @test */
    public function it_prevents_spam_with_honeypot()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.',
            'website' => 'spam-bot' // Honeypot field filled
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success'); // Should show success even for spam
    }

    /** @test */
    public function it_implements_rate_limiting()
    {
        // Send 6 messages quickly (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $contactData = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Test Subject',
                'message' => 'This is a test message with more than 10 characters.'
            ];

            $response = $this->post(route('contact.send'), $contactData);
            
            if ($i < 5) {
                $response->assertRedirect();
            } else {
                $response->assertSessionHasErrors(['email']);
            }
        }
    }

    /** @test */
    public function it_validates_disposable_email_domains()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'test@10minutemail.com', // Disposable email
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.'
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_handles_mail_sending_errors_gracefully()
    {
        Mail::shouldReceive('to')
            ->andThrow(new \Exception('Mail service unavailable'));

        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.'
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        $response->assertSessionHasErrors(['email']);
    }
}