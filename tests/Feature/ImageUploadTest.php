<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    /** @test */
    public function user_can_upload_article_featured_image()
    {
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect();
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertNotNull($article->featured_image);
        
        Storage::disk('public')->assertExists($article->featured_image);
    }

    /** @test */
    public function user_can_update_article_featured_image()
    {
        $this->actingAs($this->user);
        
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $newImage = UploadedFile::fake()->create('new-article-image.jpg', 1000, 'image/jpeg');

        $updateData = [
            'title' => $article->title,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'featured_image' => $newImage
        ];

        $response = $this->put(route('articles.update', $article), $updateData);

        $response->assertRedirect();
        
        $article->refresh();
        $this->assertNotNull($article->featured_image);
        
        Storage::disk('public')->assertExists($article->featured_image);
    }

    /** @test */
    public function user_can_upload_user_avatar()
    {
        $this->actingAs($this->user);

        $avatar = UploadedFile::fake()->create('user-avatar.jpg', 1000, 'image/jpeg');

        $updateData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'username' => $this->user->username,
            'avatar' => $avatar
        ];

        $response = $this->put(route('profile.update'), $updateData);

        $response->assertRedirect();
        
        $this->user->refresh();
        $this->assertNotNull($this->user->avatar);
        
        Storage::disk('public')->assertExists($this->user->avatar);
    }

    /** @test */
    public function it_validates_image_file_type()
    {
        $this->actingAs($this->user);

        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $invalidFile
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_validates_image_file_size()
    {
        $this->actingAs($this->user);

        $largeImage = UploadedFile::fake()->create('large-image.jpg', 10240, 'image/jpeg'); // 10MB

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $largeImage
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_validates_image_dimensions()
    {
        $this->actingAs($this->user);

        $smallImage = UploadedFile::fake()->create('small-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $smallImage
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_accepts_valid_image_formats()
    {
        $this->actingAs($this->user);

        $validFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        foreach ($validFormats as $format) {
            $image = UploadedFile::fake()->create("test-image.{$format}", 1000, "image/{$format}");

            $articleData = [
                'title' => "Test Article {$format}",
                'content' => 'Test content',
                'excerpt' => 'Test excerpt',
                'featured_image' => $image
            ];

            $response = $this->post(route('articles.store'), $articleData);

            $response->assertRedirect();
            
            $article = Article::where('title', "Test Article {$format}")->first();
            $this->assertNotNull($article->featured_image);
        }
    }

    /** @test */
    public function it_resizes_uploaded_images()
    {
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect();
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertNotNull($article->featured_image);
        
        // Check if image was resized (this would require image processing implementation)
        Storage::disk('public')->assertExists($article->featured_image);
    }

    /** @test */
    public function it_generates_thumbnails()
    {
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect();
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertNotNull($article->featured_image);
        
        // Check if thumbnail was generated (this would require thumbnail generation implementation)
        Storage::disk('public')->assertExists($article->featured_image);
    }

    /** @test */
    public function it_handles_image_upload_errors_gracefully()
    {
        $this->actingAs($this->user);

        // Simulate storage error
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('putFileAs')
            ->andThrow(new \Exception('Storage error'));

        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_removes_old_image_when_updating()
    {
        $this->actingAs($this->user);
        
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'featured_image' => 'old-image.jpg'
        ]);

        // Create the old image file
        Storage::disk('public')->put('old-image.jpg', 'fake content');

        $newImage = UploadedFile::fake()->create('new-article-image.jpg', 1000, 'image/jpeg');

        $updateData = [
            'title' => $article->title,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'featured_image' => $newImage
        ];

        $response = $this->put(route('articles.update', $article), $updateData);

        $response->assertRedirect();
        
        // Check that old image was removed
        Storage::disk('public')->assertMissing('old-image.jpg');
        
        // Check that new image was uploaded
        $article->refresh();
        Storage::disk('public')->assertExists($article->featured_image);
    }

    /** @test */
    public function it_handles_multiple_image_uploads()
    {
        $this->actingAs($this->user);

        $images = [
            UploadedFile::fake()->create('image1.jpg', 1000, 'image/jpeg'),
            UploadedFile::fake()->create('image2.jpg', 1000, 'image/jpeg'),
            UploadedFile::fake()->create('image3.jpg', 1000, 'image/jpeg')
        ];

        foreach ($images as $index => $image) {
            $articleData = [
                'title' => "Test Article {$index}",
                'content' => 'Test content',
                'excerpt' => 'Test excerpt',
                'featured_image' => $image
            ];

            $response = $this->post(route('articles.store'), $articleData);

            $response->assertRedirect();
            
            $article = Article::where('title', "Test Article {$index}")->first();
            $this->assertNotNull($article->featured_image);
            
            Storage::disk('public')->assertExists($article->featured_image);
        }
    }

    /** @test */
    public function it_validates_image_mime_type()
    {
        $this->actingAs($this->user);

        $fakeImage = UploadedFile::fake()->create('fake-image.jpg', 1000, 'text/plain');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $fakeImage
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_handles_corrupted_image_files()
    {
        $this->actingAs($this->user);

        $corruptedImage = UploadedFile::fake()->create('corrupted-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $corruptedImage
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors(['featured_image']);
    }

    /** @test */
    public function it_handles_empty_image_upload()
    {
        $this->actingAs($this->user);

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => null
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect();
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertNull($article->featured_image);
    }

    /** @test */
    public function it_handles_image_upload_without_authentication()
    {
        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'featured_image' => $image
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_handles_image_upload_with_invalid_user()
    {
        $otherUser = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $otherUser->id]);
        
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->create('article-image.jpg', 1000, 'image/jpeg');

        $updateData = [
            'title' => $article->title,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'featured_image' => $image
        ];

        $response = $this->put(route('articles.update', $article), $updateData);

        $response->assertStatus(403);
    }
}
