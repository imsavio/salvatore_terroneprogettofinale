<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tag;
use App\Models\Article;
use App\Models\User;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    protected $tag;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tag = Tag::factory()->create();
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'slug',
            'description',
            'color',
        ];

        $this->assertEquals($fillable, $this->tag->getFillable());
    }

    /** @test */
    public function it_belongs_to_many_articles()
    {
        $user = User::factory()->create();
        $article1 = Article::factory()->create(['user_id' => $user->id]);
        $article2 = Article::factory()->create(['user_id' => $user->id]);
        
        $this->tag->articles()->attach([$article1->id, $article2->id]);

        $this->assertCount(2, $this->tag->articles);
        $this->assertTrue($this->tag->articles->contains($article1));
        $this->assertTrue($this->tag->articles->contains($article2));
    }

    /** @test */
    public function it_generates_slug_on_creation()
    {
        $tag = Tag::create([
            'name' => 'Test Tag Name',
            'description' => 'Test description'
        ]);

        $this->assertEquals('test-tag-name', $tag->slug);
    }

    /** @test */
    public function it_generates_unique_slug_when_duplicate()
    {
        Tag::create([
            'name' => 'Test Tag',
            'description' => 'Test description'
        ]);

        $tag2 = Tag::create([
            'name' => 'Test Tag',
            'description' => 'Test description'
        ]);

        $this->assertEquals('test-tag-1', $tag2->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes()
    {
        $this->tag->update(['name' => 'Updated Tag Name']);
        
        $this->assertEquals('updated-tag-name', $this->tag->fresh()->slug);
    }

    /** @test */
    public function it_uses_slug_as_route_key()
    {
        $this->assertEquals('slug', $this->tag->getRouteKeyName());
    }

    /** @test */
    public function it_can_detach_articles()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        
        $this->tag->articles()->attach($article);

        $this->assertTrue($this->tag->articles->contains($article));

        $this->tag->articles()->detach($article);

        $this->assertFalse($this->tag->fresh()->articles->contains($article));
    }

    /** @test */
    public function it_can_sync_articles()
    {
        $user = User::factory()->create();
        $article1 = Article::factory()->create(['user_id' => $user->id]);
        $article2 = Article::factory()->create(['user_id' => $user->id]);
        $article3 = Article::factory()->create(['user_id' => $user->id]);

        $this->tag->articles()->attach([$article1->id, $article2->id]);

        $this->tag->articles()->sync([$article2->id, $article3->id]);

        $this->assertFalse($this->tag->fresh()->articles->contains($article1));
        $this->assertTrue($this->tag->fresh()->articles->contains($article2));
        $this->assertTrue($this->tag->fresh()->articles->contains($article3));
    }

    /** @test */
    public function it_can_get_articles_count()
    {
        $user = User::factory()->create();
        Article::factory()->count(3)->create(['user_id' => $user->id])
            ->each(function ($article) {
                $this->tag->articles()->attach($article);
            });

        $this->assertEquals(3, $this->tag->articles()->count());
    }

    /** @test */
    public function it_can_get_published_articles()
    {
        $user = User::factory()->create();
        $publishedArticle = Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay()
        ]);
        $draftArticle = Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => null
        ]);

        $this->tag->articles()->attach([$publishedArticle->id, $draftArticle->id]);

        $publishedArticles = $this->tag->publishedArticles();

        $this->assertTrue($publishedArticles->contains($publishedArticle));
        $this->assertFalse($publishedArticles->contains($draftArticle));
    }

    /** @test */
    public function it_can_get_published_articles_count()
    {
        $user = User::factory()->create();
        $publishedArticle = Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay()
        ]);
        $draftArticle = Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => null
        ]);

        $this->tag->articles()->attach([$publishedArticle->id, $draftArticle->id]);

        $this->assertEquals(1, $this->tag->publishedArticles()->count());
    }

    /** @test */
    public function it_handles_empty_name_for_slug_generation()
    {
        $tag = new Tag([
            'description' => 'Test description'
        ]);

        $tag->name = '';
        $tag->save();

        $this->assertEmpty($tag->slug);
    }

    /** @test */
    public function it_handles_special_characters_in_name()
    {
        $tag = Tag::create([
            'name' => 'Test Tag with Special Characters! @#$%^&*()',
            'description' => 'Test description'
        ]);

        $this->assertEquals('test-tag-with-special-characters', $tag->slug);
    }

    /** @test */
    public function it_handles_unicode_characters_in_name()
    {
        $tag = Tag::create([
            'name' => 'Test Tag with Unicode: café, naïve, résumé',
            'description' => 'Test description'
        ]);

        $this->assertEquals('test-tag-with-unicode-cafe-naive-resume', $tag->slug);
    }

    /** @test */
    public function it_can_get_articles_with_pagination()
    {
        $user = User::factory()->create();
        Article::factory()->count(15)->create(['user_id' => $user->id])
            ->each(function ($article) {
                $this->tag->articles()->attach($article);
            });

        $paginatedArticles = $this->tag->articles()->paginate(10);

        $this->assertCount(10, $paginatedArticles->items());
        $this->assertEquals(15, $paginatedArticles->total());
    }

    /** @test */
    public function it_can_get_most_popular_tags()
    {
        $user = User::factory()->create();
        
        // Create tags with different article counts
        $popularTag = Tag::factory()->create(['name' => 'Popular Tag']);
        $mediumTag = Tag::factory()->create(['name' => 'Medium Tag']);
        $unpopularTag = Tag::factory()->create(['name' => 'Unpopular Tag']);

        // Create articles and attach tags
        Article::factory()->count(5)->create(['user_id' => $user->id])
            ->each(function ($article) use ($popularTag) {
                $popularTag->articles()->attach($article);
            });

        Article::factory()->count(3)->create(['user_id' => $user->id])
            ->each(function ($article) use ($mediumTag) {
                $mediumTag->articles()->attach($article);
            });

        Article::factory()->count(1)->create(['user_id' => $user->id])
            ->each(function ($article) use ($unpopularTag) {
                $unpopularTag->articles()->attach($article);
            });

        $popularTags = Tag::popular()->get();

        $this->assertTrue($popularTags->contains($popularTag));
        $this->assertTrue($popularTags->contains($mediumTag));
        $this->assertTrue($popularTags->contains($unpopularTag));
    }

    /** @test */
    public function it_can_search_tags_by_name()
    {
        Tag::factory()->create(['name' => 'Laravel']);
        Tag::factory()->create(['name' => 'PHP']);
        Tag::factory()->create(['name' => 'JavaScript']);

        $searchResults = Tag::search('Laravel')->get();

        $this->assertCount(1, $searchResults);
        $this->assertEquals('Laravel', $searchResults->first()->name);
    }

    /** @test */
    public function it_can_search_tags_by_description()
    {
        Tag::factory()->create([
            'name' => 'Web Development',
            'description' => 'Everything related to web development'
        ]);
        Tag::factory()->create([
            'name' => 'Mobile Development',
            'description' => 'Everything related to mobile development'
        ]);

        $searchResults = Tag::search('web development')->get();

        $this->assertCount(1, $searchResults);
        $this->assertEquals('Web Development', $searchResults->first()->name);
    }

    /** @test */
    public function it_can_get_tags_with_article_count()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);

        Article::factory()->count(2)->create(['user_id' => $user->id])
            ->each(function ($article) use ($tag1) {
                $tag1->articles()->attach($article);
            });

        Article::factory()->count(1)->create(['user_id' => $user->id])
            ->each(function ($article) use ($tag2) {
                $tag2->articles()->attach($article);
            });

        $tagsWithCount = Tag::withCount('articles')->get();

        $this->assertEquals(2, $tagsWithCount->firstWhere('name', 'Tag 1')->articles_count);
        $this->assertEquals(1, $tagsWithCount->firstWhere('name', 'Tag 2')->articles_count);
    }
}
