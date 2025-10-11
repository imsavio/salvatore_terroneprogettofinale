<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function homepage_loads_quickly()
    {
        // Create test data
        $user = User::factory()->create();
        Article::factory()->count(50)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $startTime = microtime(true);
        
        $response = $this->get('/');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $loadTime, 'Homepage should load in less than 500ms');
    }

    /** @test */
    public function articles_index_performs_well_with_large_dataset()
    {
        // Create large dataset
        $users = User::factory()->count(10)->create();
        $articles = collect();
        
        for ($i = 0; $i < 1000; $i++) {
            $articles->push(Article::factory()->create([
                'user_id' => $users->random()->id,
                'published_at' => now()->subDays(rand(1, 365))
            ]));
        }

        $startTime = microtime(true);
        
        $response = $this->get('/articles');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(1000, $loadTime, 'Articles index should load in less than 1000ms');
    }

    /** @test */
    public function database_queries_are_optimized()
    {
        $user = User::factory()->create();
        $articles = Article::factory()->count(20)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        // Attach tags to articles
        $tags = Tag::factory()->count(10)->create();
        foreach ($articles as $article) {
            $article->tags()->attach($tags->random(rand(1, 3)));
        }

        DB::enableQueryLog();

        $response = $this->get('/articles');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);
        $this->assertLessThan(10, $queryCount, 'Should use less than 10 database queries');
    }

    /** @test */
    public function search_performance_is_acceptable()
    {
        // Create test data
        $user = User::factory()->create();
        Article::factory()->count(100)->create([
            'user_id' => $user->id,
            'title' => 'Test Article ' . rand(1, 1000),
            'content' => 'This is test content for performance testing.',
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $startTime = microtime(true);
        
        $response = $this->get('/articles?search=test');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(800, $loadTime, 'Search should complete in less than 800ms');
    }

    /** @test */
    public function memory_usage_is_reasonable()
    {
        $initialMemory = memory_get_usage(true);

        // Create test data
        $user = User::factory()->create();
        Article::factory()->count(50)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $response = $this->get('/articles');

        $finalMemory = memory_get_usage(true);
        $memoryUsed = ($finalMemory - $initialMemory) / 1024 / 1024; // Convert to MB

        $response->assertStatus(200);
        $this->assertLessThan(50, $memoryUsed, 'Memory usage should be less than 50MB');
    }

    /** @test */
    public function concurrent_requests_handle_well()
    {
        $user = User::factory()->create();
        Article::factory()->count(20)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $responses = [];
        $startTime = microtime(true);

        // Simulate concurrent requests
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->get('/articles');
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        $this->assertLessThan(2000, $totalTime, '10 concurrent requests should complete in less than 2000ms');
    }

    /** @test */
    public function image_upload_performance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $image = \Illuminate\Http\UploadedFile::fake()->create('test-image.jpg', 1000, 'image/jpeg');

        $startTime = microtime(true);

        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ]);

        $endTime = microtime(true);
        $uploadTime = ($endTime - $startTime) * 1000;

        $response->assertRedirect();
        $this->assertLessThan(3000, $uploadTime, 'Image upload should complete in less than 3000ms');
    }

    /** @test */
    public function cache_performance()
    {
        // Clear cache first
        \Illuminate\Support\Facades\Cache::flush();

        $user = User::factory()->create();
        Article::factory()->count(20)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        // First request (no cache)
        $startTime = microtime(true);
        $response1 = $this->get('/articles');
        $endTime = microtime(true);
        $firstLoadTime = ($endTime - $startTime) * 1000;

        // Second request (with cache)
        $startTime = microtime(true);
        $response2 = $this->get('/articles');
        $endTime = microtime(true);
        $secondLoadTime = ($endTime - $startTime) * 1000;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Cached request should be significantly faster
        $this->assertLessThan($firstLoadTime * 0.5, $secondLoadTime, 'Cached request should be at least 50% faster');
    }

    /** @test */
    public function database_connection_pooling()
    {
        $connections = [];
        $startTime = microtime(true);

        // Simulate multiple database connections
        for ($i = 0; $i < 20; $i++) {
            $connections[] = DB::connection();
        }

        $endTime = microtime(true);
        $connectionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $connectionTime, 'Database connections should be established quickly');
    }

    /** @test */
    public function api_response_times()
    {
        $user = User::factory()->create();
        Article::factory()->count(20)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $endpoints = [
            '/api/articles',
            '/api/tags',
            '/health',
            '/health/detailed'
        ];

        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            $response = $this->get($endpoint);
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $response->assertStatus(200);
            $this->assertLessThan(500, $responseTime, "API endpoint {$endpoint} should respond in less than 500ms");
        }
    }
}
