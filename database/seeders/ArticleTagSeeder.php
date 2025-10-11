<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users first
        $users = \App\Models\User::factory(3)->create();
        
        // Create predefined tags
        $tagNames = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 
            'React', 'Bootstrap', 'CSS', 'HTML'
        ];
        
        $tags = [];
        foreach ($tagNames as $name) {
            $tags[] = \App\Models\Tag::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'color' => $this->getRandomColor(),
            ]);
        }
        
        // Create articles
        $articles = \App\Models\Article::factory(15)->create([
            'user_id' => $users->random()->id,
        ]);
        
        // Attach random tags to articles
        foreach ($articles as $article) {
            $randomTags = $tags->random(rand(1, min(4, $tags->count())));
            $article->tags()->attach($randomTags);
        }
        
        // Create some published articles
        \App\Models\Article::factory(10)->published()->create([
            'user_id' => $users->random()->id,
        ])->each(function ($article) use ($tags) {
            $randomTags = $tags->random(rand(1, 3));
            $article->tags()->attach($randomTags);
        });
        
        // Create some draft articles
        \App\Models\Article::factory(5)->draft()->create([
            'user_id' => $users->random()->id,
        ])->each(function ($article) use ($tags) {
            $randomTags = $tags->random(rand(1, 2));
            $article->tags()->attach($randomTags);
        });
    }
    
    private function getRandomColor(): string
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
        ];
        
        return $colors[array_rand($colors)];
    }
}
