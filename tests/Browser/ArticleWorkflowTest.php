<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;

class ArticleWorkflowTest extends DuskTestCase
{
    /** @test */
    public function user_can_create_article()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/articles/create')
                    ->type('title', 'Test Article')
                    ->type('content', 'This is test content')
                    ->type('excerpt', 'Test excerpt')
                    ->press('Pubblica')
                    ->assertPathIs('/articles')
                    ->assertSee('Test Article');
        });
    }

    /** @test */
    public function user_can_edit_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $article) {
            $browser->loginAs($user)
                    ->visit("/articles/{$article->slug}/edit")
                    ->type('title', 'Updated Article Title')
                    ->press('Aggiorna')
                    ->assertPathIs('/articles')
                    ->assertSee('Updated Article Title');
        });
    }

    /** @test */
    public function user_can_delete_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $article) {
            $browser->loginAs($user)
                    ->visit("/articles/{$article->slug}")
                    ->press('Elimina')
                    ->acceptDialog()
                    ->assertPathIs('/articles')
                    ->assertDontSee($article->title);
        });
    }
}
