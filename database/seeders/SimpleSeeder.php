<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;

class SimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'username' => 'admin',
                'bio' => 'Administrator of the blog',
                'is_admin' => true,
            ]
        );

        // Create sample user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'username' => 'testuser',
                'bio' => 'Test user for the blog',
                'is_admin' => false,
            ]
        );

        // Create sample tags
        $tags = [
            ['name' => 'Laravel', 'color' => '#FF2D20'],
            ['name' => 'PHP', 'color' => '#777BB4'],
            ['name' => 'JavaScript', 'color' => '#F7DF1E'],
            ['name' => 'Vue.js', 'color' => '#4FC08D'],
            ['name' => 'React', 'color' => '#61DAFB'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(['name' => $tagData['name']], $tagData);
        }

        // Create sample articles
        $articles = [
            [
                'title' => 'Getting Started with Laravel 10',
                'content' => 'Laravel 10 is the latest version of the popular PHP framework. In this article, we will explore the new features and improvements.',
                'excerpt' => 'Learn about the new features in Laravel 10 and how to get started with this powerful PHP framework.',
                'user_id' => $admin->id,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Building Modern Web Applications with Vue.js',
                'content' => 'Vue.js is a progressive JavaScript framework that makes building user interfaces easy and enjoyable.',
                'excerpt' => 'Discover how to build modern web applications using Vue.js and its ecosystem.',
                'user_id' => $user->id,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'PHP Best Practices for 2024',
                'content' => 'PHP has evolved significantly over the years. Here are the best practices you should follow in 2024.',
                'excerpt' => 'Stay up to date with the latest PHP best practices and coding standards.',
                'user_id' => $admin->id,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($articles as $articleData) {
            $article = Article::create($articleData);
            
            // Attach random tags to articles
            $randomTags = Tag::inRandomOrder()->limit(rand(1, 3))->get();
            $article->tags()->attach($randomTags);
        }

        $this->command->info('Sample data created successfully!');
    }
}
