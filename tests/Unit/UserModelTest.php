<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'email',
            'password',
            'username',
            'bio',
            'avatar',
            'website',
            'location',
            'is_admin',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /** @test */
    public function it_has_hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /** @test */
    public function it_has_many_articles()
    {
        $article1 = Article::factory()->create(['user_id' => $this->user->id]);
        $article2 = Article::factory()->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->articles);
        $this->assertTrue($this->user->articles->contains($article1));
        $this->assertTrue($this->user->articles->contains($article2));
    }

    /** @test */
    public function it_can_create_articles()
    {
        $articleData = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'published_at' => now()
        ];

        $article = $this->user->articles()->create($articleData);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals($this->user->id, $article->user_id);
        $this->assertEquals('Test Article', $article->title);
    }

    /** @test */
    public function it_has_many_tags_through_articles()
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        
        $article->tags()->attach([$tag1->id, $tag2->id]);

        $userTags = $this->user->tags();
        $this->assertCount(2, $userTags->get());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        $adminUser = User::factory()->create(['is_admin' => true]);

        $this->assertFalse($regularUser->is_admin);
        $this->assertTrue($adminUser->is_admin);
    }

    /** @test */
    public function it_hashes_password_when_set()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_can_update_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        
        $user->update(['password' => 'new-password']);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function it_can_get_published_articles()
    {
        $publishedArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now()->subDay()
        ]);

        $draftArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => null
        ]);

        $publishedArticles = $this->user->publishedArticles();

        $this->assertTrue($publishedArticles->contains($publishedArticle));
        $this->assertFalse($publishedArticles->contains($draftArticle));
    }

    /** @test */
    public function it_can_get_draft_articles()
    {
        $publishedArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now()->subDay()
        ]);

        $draftArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => null
        ]);

        $draftArticles = $this->user->draftArticles();

        $this->assertFalse($draftArticles->contains($publishedArticle));
        $this->assertTrue($draftArticles->contains($draftArticle));
    }

    /** @test */
    public function it_can_get_articles_count()
    {
        Article::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->assertEquals(3, $this->user->articles()->count());
    }

    /** @test */
    public function it_can_get_published_articles_count()
    {
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'published_at' => now()->subDay()
        ]);

        Article::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'published_at' => null
        ]);

        $this->assertEquals(2, $this->user->publishedArticles()->count());
    }

    /** @test */
    public function it_can_get_draft_articles_count()
    {
        Article::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'published_at' => now()->subDay()
        ]);

        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'published_at' => null
        ]);

        $this->assertEquals(2, $this->user->draftArticles()->count());
    }

    /** @test */
    public function it_can_get_avatar_url()
    {
        $user = User::factory()->create(['avatar' => 'avatars/user123.jpg']);

        $this->assertStringContainsString('avatars/user123.jpg', $user->avatar_url);
    }

    /** @test */
    public function it_returns_default_avatar_when_no_avatar_set()
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertStringContainsString('default-avatar', $user->avatar_url);
    }

    /** @test */
    public function it_can_get_full_name()
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->full_name);
    }

    /** @test */
    public function it_can_get_display_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'username' => 'johndoe'
        ]);

        $this->assertEquals('johndoe', $user->display_name);
    }

    /** @test */
    public function it_falls_back_to_name_for_display_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'username' => null
        ]);

        $this->assertEquals('John Doe', $user->display_name);
    }

    /** @test */
    public function it_can_check_if_user_has_articles()
    {
        $userWithoutArticles = User::factory()->create();
        $userWithArticles = User::factory()->create();

        Article::factory()->create(['user_id' => $userWithArticles->id]);

        $this->assertFalse($userWithoutArticles->hasArticles());
        $this->assertTrue($userWithArticles->hasArticles());
    }

    /** @test */
    public function it_can_check_if_user_has_published_articles()
    {
        $user = User::factory()->create();

        Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay()
        ]);

        Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => null
        ]);

        $this->assertTrue($user->hasPublishedArticles());
    }

    /** @test */
    public function it_can_get_recent_articles()
    {
        $oldArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(10)
        ]);

        $recentArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDay()
        ]);

        $recentArticles = $this->user->recentArticles(1);

        $this->assertTrue($recentArticles->contains($recentArticle));
        $this->assertFalse($recentArticles->contains($oldArticle));
    }
}
