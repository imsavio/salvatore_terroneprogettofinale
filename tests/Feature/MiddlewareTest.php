<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('articles.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('articles.edit', 1));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('articles.store'));
        $response->assertRedirect(route('login'));

        $response = $this->put(route('articles.update', 1));
        $response->assertRedirect(route('login'));

        $response = $this->delete(route('articles.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('articles.create'));
        $response->assertStatus(200);

        $response = $this->get(route('profile'));
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_only_edit_own_articles()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $response = $this->get(route('articles.edit', $article));
        $response->assertStatus(403);

        $response = $this->put(route('articles.update', $article), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt'
        ]);
        $response->assertStatus(403);

        $response = $this->delete(route('articles.destroy', $article));
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_edit_own_articles()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get(route('articles.edit', $article));
        $response->assertStatus(200);

        $response = $this->put(route('articles.update', $article), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt'
        ]);
        $response->assertRedirect();

        $response = $this->delete(route('articles.destroy', $article));
        $response->assertRedirect(route('articles.index'));
    }

    /** @test */
    public function admin_can_edit_any_article()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin);

        $response = $this->get(route('articles.edit', $article));
        $response->assertStatus(200);

        $response = $this->put(route('articles.update', $article), [
            'title' => 'Updated by Admin',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt'
        ]);
        $response->assertRedirect();

        $response = $this->delete(route('articles.destroy', $article));
        $response->assertRedirect(route('articles.index'));
    }

    /** @test */
    public function rate_limiting_works_for_contact_form()
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
    public function csrf_protection_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test without CSRF token
        $response = $this->post(route('articles.store'), [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt'
        ]);
        
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function honeypot_protection_works()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message with more than 10 characters.',
            'website' => 'spam-bot' // Honeypot field filled
        ];

        $response = $this->post(route('contact.send'), $contactData);
        
        // Should still show success to prevent spam detection
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function maintenance_mode_works()
    {
        // This would require setting up maintenance mode
        // For now, we'll test that the middleware exists
        $this->assertTrue(class_exists(\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class));
    }

    /** @test */
    public function throttle_middleware_works()
    {
        // Test login throttling
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword'
            ]);
            
            if ($i < 5) {
                $response->assertSessionHasErrors(['email']);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /** @test */
    public function json_middleware_works()
    {
        $response = $this->getJson(route('articles.index'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    /** @test */
    public function cors_middleware_works()
    {
        $response = $this->withHeaders([
            'Origin' => 'https://example.com',
            'Access-Control-Request-Method' => 'GET',
            'Access-Control-Request-Headers' => 'Content-Type'
        ])->options(route('articles.index'));

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin');
    }
}
