<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_view_login_form()
    {
        $response = $this->get(route('login'));
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Login');
    }

    /** @test */
    public function user_can_view_register_form()
    {
        $response = $this->get(route('register'));
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register');
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);
        
        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_cannot_register_with_invalid_email()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);
        
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function user_cannot_register_with_mismatched_passwords()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ];

        $response = $this->post(route('register'), $userData);
        
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);
        
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));
        
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('articles.create'));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('articles.create'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $this->actingAs($user);

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'username' => 'jane_doe',
            'bio' => 'Updated bio',
        ];

        $response = $this->put(route('profile.update'), $updateData);
        
        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'username' => 'jane_doe',
            'bio' => 'Updated bio',
        ]);
    }

    /** @test */
    public function user_cannot_use_existing_email_in_profile_update()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $otherUser = User::factory()->create(['email' => 'jane@example.com']);
        $this->actingAs($user);

        $updateData = [
            'name' => 'John Doe',
            'email' => 'jane@example.com', // Existing email
            'username' => 'john_doe',
        ];

        $response = $this->put(route('profile.update'), $updateData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        $this->actingAs($user);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ];

        $response = $this->put(route('profile.update'), $updateData);
        
        $response->assertRedirect(route('profile'));
        $this->assertTrue(password_verify('new-password', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        $this->actingAs($user);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ];

        $response = $this->put(route('profile.update'), $updateData);
        
        $response->assertSessionHasErrors(['current_password']);
    }
}