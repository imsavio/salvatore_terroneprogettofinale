<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@blog.com',
            'password' => Hash::make('admin123'),
            'username' => 'admin',
            'bio' => 'Administrator of the blog',
            'is_admin' => true,
        ]);

        // Create sample users
        $users = User::factory()->count(5)->create();

        // Create sample tags
        $tags = [
            ['name' => 'Laravel', 'color' => '#FF2D20'],
            ['name' => 'PHP', 'color' => '#777BB4'],
            ['name' => 'JavaScript', 'color' => '#F7DF1E'],
            ['name' => 'Vue.js', 'color' => '#4FC08D'],
            ['name' => 'React', 'color' => '#61DAFB'],
            ['name' => 'CSS', 'color' => '#1572B6'],
            ['name' => 'HTML', 'color' => '#E34F26'],
            ['name' => 'MySQL', 'color' => '#4479A1'],
            ['name' => 'PostgreSQL', 'color' => '#336791'],
            ['name' => 'Docker', 'color' => '#2496ED'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        // Create sample articles
        $articles = Article::factory()->count(20)->create([
            'user_id' => $users->random()->id,
            'published_at' => now()->subDays(rand(1, 30)),
        ]);

        // Attach random tags to articles
        foreach ($articles as $article) {
            $randomTags = Tag::inRandomOrder()->limit(rand(1, 3))->get();
            $article->tags()->attach($randomTags);
        }

        // Create some draft articles
        Article::factory()->count(5)->create([
            'user_id' => $users->random()->id,
            'published_at' => null,
        ]);
    }
}
