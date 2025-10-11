<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;

class HomepageTest extends DuskTestCase
{
    /** @test */
    public function it_displays_homepage_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Blog')
                    ->assertSee('Articoli')
                    ->assertSee('Contatti');
        });
    }

    /** @test */
    public function it_displays_articles_on_homepage()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay()
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit('/')
                    ->assertSee($article->title)
                    ->assertSee($article->excerpt);
        });
    }

    /** @test */
    public function it_has_responsive_design()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->resize(375, 667) // Mobile
                    ->assertSee('Blog')
                    ->resize(768, 1024) // Tablet
                    ->assertSee('Blog')
                    ->resize(1920, 1080) // Desktop
                    ->assertSee('Blog');
        });
    }
}
