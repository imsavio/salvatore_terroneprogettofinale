<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'excerpt' => $this->faker->paragraph(2),
            'body' => collect(range(1, 5))->map(fn () => $this->faker->paragraph(5))->implode("\n\n"),
            'cover_image' => null,
            'published_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', '+1 week'),
        ];
    }
}
