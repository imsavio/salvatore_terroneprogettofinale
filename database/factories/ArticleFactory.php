<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
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
            'user_id' => \App\Models\User::factory(),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(6),
            'excerpt' => $this->faker->paragraph(2),
            'body' => collect(range(1, 5))->map(fn () => $this->faker->paragraph(5))->implode("\n\n"),
            'cover_image' => null,
            'published_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', '+1 week'),
        ];
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the article is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
