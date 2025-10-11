<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;

class ArticleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function it_can_display_articles_index()
    {
        $response = $this->get(route('articles.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function it_can_display_single_article()
    {
        $response = $this->get(route('articles.show', $this->article));
        
        $response->assertStatus(200);
        $response->assertViewIs('articles.show');
        $response->assertSee($this->article->title);
        $response->assertSee($this->article->content);
    }

    /** @test */
    public function it_can_search_articles()
    {
        $response = $this->get(route('articles.index', ['search' => $this->article->title]));
        
        $response->assertStatus(200);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function it_can_filter_articles_by_tag()
    {
        $tag = Tag::factory()->create();
        $this->article->tags()->attach($tag);

        $response = $this->get(route('articles.index', ['tag' => $tag->slug]));
        
        $response->assertStatus(200);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function it_can_filter_articles_by_author()
    {
        $response = $this->get(route('articles.index', ['author' => $this->user->id]));
        
        $response->assertStatus(200);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function it_requires_authentication_to_create_article()
    {
        $response = $this->get(route('articles.create'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_create_article()
    {
        $this->actingAs($this->user);
        
        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is test content',
            'excerpt' => 'Test excerpt',
            'published_at' => now()->format('Y-m-d\TH:i')
        ];

        $response = $this->post(route('articles.store'), $articleData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_article()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('articles.store'), []);
        
        $response->assertSessionHasErrors(['title', 'content']);
    }

    /** @test */
    public function user_can_edit_own_article()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('articles.edit', $this->article));
        
        $response->assertStatus(200);
        $response->assertViewIs('articles.edit');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function user_cannot_edit_other_users_article()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        
        $response = $this->get(route('articles.edit', $this->article));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_own_article()
    {
        $this->actingAs($this->user);
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt',
            'published_at' => now()->format('Y-m-d\TH:i')
        ];

        $response = $this->put(route('articles.update', $this->article), $updateData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'Updated Title'
        ]);
    }

    /** @test */
    public function user_can_delete_own_article()
    {
        $this->actingAs($this->user);
        
        $response = $this->delete(route('articles.destroy', $this->article));
        
        $response->assertRedirect(route('articles.index'));
        $this->assertDatabaseMissing('articles', [
            'id' => $this->article->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_users_article()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        
        $response = $this->delete(route('articles.destroy', $this->article));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_save_article_as_draft()
    {
        $this->actingAs($this->user);
        
        $articleData = [
            'title' => 'Draft Article',
            'content' => 'This is draft content',
            'excerpt' => 'Draft excerpt',
            'action' => 'draft'
        ];

        $response = $this->post(route('articles.store'), $articleData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Draft Article',
            'published_at' => null
        ]);
    }

    /** @test */
    public function it_can_attach_tags_to_article()
    {
        $this->actingAs($this->user);
        
        $tag = Tag::factory()->create();
        
        $articleData = [
            'title' => 'Article with Tags',
            'content' => 'This article has tags',
            'excerpt' => 'Tagged article',
            'tags' => [$tag->id]
        ];

        $response = $this->post(route('articles.store'), $articleData);
        
        $response->assertRedirect();
        
        $article = Article::where('title', 'Article with Tags')->first();
        $this->assertTrue($article->tags->contains($tag));
    }
}