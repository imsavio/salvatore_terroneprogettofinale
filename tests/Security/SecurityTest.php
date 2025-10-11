<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sql_injection_protection()
    {
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "' OR '1'='1",
            "1' UNION SELECT * FROM users --",
            "admin'--",
            "' OR 1=1 --"
        ];

        foreach ($maliciousInputs as $input) {
            $response = $this->get("/articles?search=" . urlencode($input));
            $response->assertStatus(200);
            
            // Ensure no SQL error occurred
            $this->assertStringNotContainsString('SQLSTATE', $response->getContent());
            $this->assertStringNotContainsString('syntax error', $response->getContent());
        }
    }

    /** @test */
    public function xss_protection()
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(\'XSS\')">',
            '<svg onload="alert(\'XSS\')">',
            'javascript:alert("XSS")',
            '<iframe src="javascript:alert(\'XSS\')"></iframe>'
        ];

        $user = User::factory()->create();
        $this->actingAs($user);

        foreach ($xssPayloads as $payload) {
            $response = $this->post('/articles', [
                'title' => $payload,
                'content' => $payload,
                'excerpt' => $payload
            ]);

            $response->assertRedirect();
            
            // Check that the payload is escaped in the database
            $article = Article::latest()->first();
            $this->assertStringNotContainsString('<script>', $article->title);
            $this->assertStringNotContainsString('onerror=', $article->content);
        }
    }

    /** @test */
    public function csrf_protection()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test without CSRF token
        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'content' => 'Test content'
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function authentication_required_for_protected_routes()
    {
        $protectedRoutes = [
            'articles.create',
            'articles.store',
            'articles.edit',
            'articles.update',
            'articles.destroy',
            'profile',
            'profile.update'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get(route($route, ['article' => 1]));
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function authorization_prevents_unauthorized_access()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user1->id]);

        $this->actingAs($user2);

        // User2 should not be able to edit user1's article
        $response = $this->get(route('articles.edit', $article));
        $response->assertStatus(403);

        $response = $this->put(route('articles.update', $article), [
            'title' => 'Hacked Title',
            'content' => 'Hacked content'
        ]);
        $response->assertStatus(403);

        $response = $this->delete(route('articles.destroy', $article));
        $response->assertStatus(403);
    }

    /** @test */
    public function password_requirements_enforced()
    {
        $weakPasswords = [
            '123',
            'password',
            '12345678',
            'qwerty',
            'abc123'
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => $password,
                'password_confirmation' => $password
            ]);

            $response->assertSessionHasErrors(['password']);
        }
    }

    /** @test */
    public function rate_limiting_works()
    {
        // Test login rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
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
    public function file_upload_security()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $maliciousFiles = [
            'malicious.php' => 'text/plain',
            'script.js' => 'application/javascript',
            'virus.exe' => 'application/octet-stream',
            'backdoor.php' => 'text/plain'
        ];

        foreach ($maliciousFiles as $filename => $mimeType) {
            $file = \Illuminate\Http\UploadedFile::fake()->create($filename, 1000, $mimeType);

            $response = $this->post('/articles', [
                'title' => 'Test Article',
                'content' => 'Test content',
                'featured_image' => $file
            ]);

            $response->assertSessionHasErrors(['featured_image']);
        }
    }

    /** @test */
    public function sensitive_data_not_exposed()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
            'remember_token' => 'secret-token'
        ]);

        $response = $this->get('/api/users/' . $user->id);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('remember_token', $responseData);
        $this->assertArrayNotHasKey('two_factor_secret', $responseData);
    }

    /** @test */
    public function headers_security_configured()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
    }

    /** @test */
    public function session_security()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/profile');
        
        $response->assertStatus(200);
        
        // Check session configuration
        $sessionConfig = config('session');
        $this->assertEquals('database', $sessionConfig['driver']);
        $this->assertTrue($sessionConfig['http_only']);
        $this->assertTrue($sessionConfig['secure'] || app()->environment('local'));
    }

    /** @test */
    public function input_validation_prevents_malicious_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $maliciousInputs = [
            'title' => str_repeat('A', 1000), // Too long
            'content' => '', // Empty required field
            'email' => 'not-an-email', // Invalid email
            'website' => 'javascript:alert("XSS")', // XSS in URL
        ];

        $response = $this->post('/articles', $maliciousInputs);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function directory_traversal_protection()
    {
        $maliciousPaths = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\drivers\\etc\\hosts',
            '....//....//....//etc/passwd',
            '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd'
        ];

        foreach ($maliciousPaths as $path) {
            $response = $this->get('/storage/' . $path);
            $response->assertStatus(404);
        }
    }

    /** @test */
    public function sql_injection_in_search()
    {
        $maliciousSearches = [
            "' UNION SELECT password FROM users --",
            "'; DROP TABLE articles; --",
            "' OR 1=1 --",
            "admin'--"
        ];

        foreach ($maliciousSearches as $search) {
            $response = $this->get('/articles?search=' . urlencode($search));
            $response->assertStatus(200);
            $this->assertStringNotContainsString('SQLSTATE', $response->getContent());
        }
    }

    /** @test */
    public function brute_force_protection()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Try multiple wrong passwords
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword'
            ]);

            if ($i < 5) {
                $response->assertSessionHasErrors(['email']);
            } else {
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function https_redirect_in_production()
    {
        // This test would need to be run in production environment
        // to properly test HTTPS redirect
        $this->markTestSkipped('HTTPS redirect test requires production environment');
    }

    /** @test */
    public function secure_cookies()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/profile');
        
        $cookies = $response->headers->getCookies();
        
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'laravel_session') {
                $this->assertTrue($cookie->isSecure() || app()->environment('local'));
                $this->assertTrue($cookie->isHttpOnly());
            }
        }
    }
}
