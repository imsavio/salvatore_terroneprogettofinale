<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function contact_confirmation_email_can_be_instantiated()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);

        $this->assertInstanceOf(ContactConfirmation::class, $mail);
    }

    /** @test */
    public function contact_confirmation_email_has_correct_subject()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);

        $this->assertEquals('Conferma ricezione messaggio - Test Subject', $mail->build()->subject);
    }

    /** @test */
    public function contact_confirmation_email_has_correct_recipient()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);

        $this->assertEquals('john@example.com', $mail->build()->to[0]['address']);
    }

    /** @test */
    public function contact_confirmation_email_has_correct_sender()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);

        $this->assertEquals(config('mail.from.address'), $mail->build()->from[0]['address']);
    }

    /** @test */
    public function contact_confirmation_email_contains_contact_data()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('John Doe', $rendered);
        $this->assertStringContainsString('john@example.com', $rendered);
        $this->assertStringContainsString('Test Subject', $rendered);
        $this->assertStringContainsString('Test message', $rendered);
    }

    /** @test */
    public function contact_notification_email_can_be_instantiated()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);

        $this->assertInstanceOf(ContactNotification::class, $mail);
    }

    /** @test */
    public function contact_notification_email_has_correct_subject()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);

        $this->assertEquals('Nuovo messaggio di contatto - Test Subject', $mail->build()->subject);
    }

    /** @test */
    public function contact_notification_email_has_correct_recipient()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);

        $this->assertEquals(config('mail.admin_email', config('mail.from.address')), $mail->build()->to[0]['address']);
    }

    /** @test */
    public function contact_notification_email_has_correct_sender()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);

        $this->assertEquals(config('mail.from.address'), $mail->build()->from[0]['address']);
    }

    /** @test */
    public function contact_notification_email_contains_contact_data()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('John Doe', $rendered);
        $this->assertStringContainsString('john@example.com', $rendered);
        $this->assertStringContainsString('Test Subject', $rendered);
        $this->assertStringContainsString('Test message', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_html_content()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => '<p>Test message with <strong>HTML</strong> content</p>'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Test message with HTML content', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_html_content()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => '<p>Test message with <strong>HTML</strong> content</p>'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Test message with HTML content', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_long_messages()
    {
        $longMessage = str_repeat('This is a very long message. ', 100);
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => $longMessage
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('This is a very long message.', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_long_messages()
    {
        $longMessage = str_repeat('This is a very long message. ', 100);
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => $longMessage
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('This is a very long message.', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_special_characters()
    {
        $contactData = [
            'name' => 'José María',
            'email' => 'josé@example.com',
            'subject' => 'Test Subject with Special Characters: @#$%^&*()',
            'message' => 'Test message with special characters: @#$%^&*()'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('José María', $rendered);
        $this->assertStringContainsString('josé@example.com', $rendered);
        $this->assertStringContainsString('Test Subject with Special Characters', $rendered);
        $this->assertStringContainsString('Test message with special characters', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_special_characters()
    {
        $contactData = [
            'name' => 'José María',
            'email' => 'josé@example.com',
            'subject' => 'Test Subject with Special Characters: @#$%^&*()',
            'message' => 'Test message with special characters: @#$%^&*()'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('José María', $rendered);
        $this->assertStringContainsString('josé@example.com', $rendered);
        $this->assertStringContainsString('Test Subject with Special Characters', $rendered);
        $this->assertStringContainsString('Test message with special characters', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_empty_fields()
    {
        $contactData = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => ''
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_empty_fields()
    {
        $contactData = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => ''
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_null_fields()
    {
        $contactData = [
            'name' => null,
            'email' => null,
            'subject' => null,
            'message' => null
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_null_fields()
    {
        $contactData = [
            'name' => null,
            'email' => null,
            'subject' => null,
            'message' => null
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_very_long_subject()
    {
        $longSubject = str_repeat('Very Long Subject ', 50);
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => $longSubject,
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Very Long Subject', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_very_long_subject()
    {
        $longSubject = str_repeat('Very Long Subject ', 50);
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => $longSubject,
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Very Long Subject', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_very_long_name()
    {
        $longName = str_repeat('Very Long Name ', 50);
        $contactData = [
            'name' => $longName,
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Very Long Name', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_very_long_name()
    {
        $longName = str_repeat('Very Long Name ', 50);
        $contactData = [
            'name' => $longName,
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('Very Long Name', $rendered);
    }

    /** @test */
    public function contact_confirmation_email_handles_very_long_email()
    {
        $longEmail = str_repeat('verylongemail', 20) . '@example.com';
        $contactData = [
            'name' => 'John Doe',
            'email' => $longEmail,
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactConfirmation($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('verylongemail', $rendered);
    }

    /** @test */
    public function contact_notification_email_handles_very_long_email()
    {
        $longEmail = str_repeat('verylongemail', 20) . '@example.com';
        $contactData = [
            'name' => 'John Doe',
            'email' => $longEmail,
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $mail = new ContactNotification($contactData);
        $rendered = $mail->render();

        $this->assertStringContainsString('verylongemail', $rendered);
    }
}
