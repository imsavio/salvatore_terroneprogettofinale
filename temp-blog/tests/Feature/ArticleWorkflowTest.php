<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows published articles on the index for guests', function () {
    $article = Article::factory()->create(['published_at' => now()]);

    $response = $this->get(route('articles.index'));

    $response->assertOk()
        ->assertSee($article->title);
});

it('prevents guests from viewing draft articles', function () {
    $article = Article::factory()->create(['published_at' => null]);

    $this->get(route('articles.show', $article))->assertForbidden();
});

it('allows authenticated users to create a published article', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('articles.store'), [
        'title' => 'Lancio piattaforma Nova',
        'excerpt' => 'Anteprima delle nuove funzionalità della piattaforma Nova.',
        'body' => str_repeat('Contenuto articolo molto interessante. ', 8),
        'status' => 'published',
        'tags' => 'innovazione, tecnologia',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('articles', [
        'title' => 'Lancio piattaforma Nova',
        'user_id' => $user->id,
    ]);
});
