<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Francesca Rossi',
            'email' => 'admin@novablog.test',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $contributors = User::factory()->count(3)->create();
        $tags = Tag::factory()->count(8)->create();

        $writers = $contributors->prepend($admin);

        Article::factory()
            ->count(12)
            ->make()
            ->each(function ($article) use ($writers, $tags) {
                $article->user_id = $writers->random()->id;
                $article->save();
                $article->syncTags($tags->random(rand(2, 4))->pluck('name')->all());
            });
    }
}
