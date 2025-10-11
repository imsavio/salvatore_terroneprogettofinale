<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;

class ArticleModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now()
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'content',
            'excerpt',
            'featured_image',
            'published_at',
            'user_id',
            'slug',
        ];

        $this->assertEquals($fillable, $this->article->getFillable());
    }

    /** @test */
    public function it_casts_published_at_to_datetime()
    {
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->article->published_at);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $this->assertInstanceOf(User::class, $this->article->user);
        $this->assertEquals($this->user->id, $this->article->user->id);
    }

    /** @test */
    public function it_has_many_tags()
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        
        $this->article->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $this->article->tags);
        $this->assertTrue($this->article->tags->contains($tag1));
        $this->assertTrue($this->article->tags->contains($tag2));
    }

    /** @test */
    public function it_generates_slug_on_creation()
    {
        $article = Article::create([
            'title' => 'Test Article Title',
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $this->assertEquals('test-article-title', $article->slug);
    }

    /** @test */
    public function it_generates_unique_slug_when_duplicate()
    {
        Article::create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $article2 = Article::create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $this->assertEquals('test-article-1', $article2->slug);
    }

    /** @test */
    public function it_updates_slug_when_title_changes()
    {
        $this->article->update(['title' => 'Updated Title']);
        
        $this->assertEquals('updated-title', $this->article->fresh()->slug);
    }

    /** @test */
    public function it_uses_slug_as_route_key()
    {
        $this->assertEquals('slug', $this->article->getRouteKeyName());
    }

    /** @test */
    public function it_generates_excerpt_from_content_when_empty()
    {
        $article = Article::create([
            'title' => 'Test Article',
            'content' => '<p>This is a long content that should be truncated to create an excerpt when the excerpt field is empty.</p>',
            'user_id' => $this->user->id
        ]);

        $this->assertStringContainsString('This is a long content that should be truncated', $article->excerpt);
        $this->assertLessThanOrEqual(150, strlen($article->excerpt));
    }

    /** @test */
    public function it_uses_provided_excerpt_when_not_empty()
    {
        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Custom excerpt',
            'user_id' => $this->user->id
        ]);

        $this->assertEquals('Custom excerpt', $article->excerpt);
    }

    /** @test */
    public function it_scopes_published_articles()
    {
        $publishedArticle = Article::factory()->create([
            'published_at' => now()->subDay(),
            'user_id' => $this->user->id
        ]);

        $draftArticle = Article::factory()->create([
            'published_at' => null,
            'user_id' => $this->user->id
        ]);

        $futureArticle = Article::factory()->create([
            'published_at' => now()->addDay(),
            'user_id' => $this->user->id
        ]);

        $publishedArticles = Article::published()->get();

        $this->assertTrue($publishedArticles->contains($publishedArticle));
        $this->assertTrue($publishedArticles->contains($this->article));
        $this->assertFalse($publishedArticles->contains($draftArticle));
        $this->assertFalse($publishedArticles->contains($futureArticle));
    }

    /** @test */
    public function it_scopes_draft_articles()
    {
        $draftArticle = Article::factory()->create([
            'published_at' => null,
            'user_id' => $this->user->id
        ]);

        $draftArticles = Article::draft()->get();

        $this->assertTrue($draftArticles->contains($draftArticle));
        $this->assertFalse($draftArticles->contains($this->article));
    }

    /** @test */
    public function it_can_detach_tags()
    {
        $tag = Tag::factory()->create();
        $this->article->tags()->attach($tag);

        $this->assertTrue($this->article->tags->contains($tag));

        $this->article->tags()->detach($tag);

        $this->assertFalse($this->article->fresh()->tags->contains($tag));
    }

    /** @test */
    public function it_can_sync_tags()
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $tag3 = Tag::factory()->create();

        $this->article->tags()->attach([$tag1->id, $tag2->id]);

        $this->article->tags()->sync([$tag2->id, $tag3->id]);

        $this->assertFalse($this->article->fresh()->tags->contains($tag1));
        $this->assertTrue($this->article->fresh()->tags->contains($tag2));
        $this->assertTrue($this->article->fresh()->tags->contains($tag3));
    }

    /** @test */
    public function it_handles_empty_title_for_slug_generation()
    {
        $article = new Article([
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $article->title = '';
        $article->save();

        $this->assertEquals('untitled', $article->slug);
    }

    /** @test */
    public function it_handles_special_characters_in_title()
    {
        $article = Article::create([
            'title' => 'Test Article with Special Characters! @#$%^&*()',
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $this->assertEquals('test-article-with-special-characters-at', $article->slug);
    }

    /** @test */
    public function it_handles_unicode_characters_in_title()
    {
        $article = Article::create([
            'title' => 'Test Article with Unicode: café, naïve, résumé',
            'content' => 'Test content',
            'user_id' => $this->user->id
        ]);

        $this->assertEquals('test-article-with-unicode-cafe-naive-resume', $article->slug);
    }
}
