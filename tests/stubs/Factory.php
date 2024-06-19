<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->lexify('?????????'),
            'slug' => $this->faker->unique()->lexify('?????????'),
            'excerpt' => $this->faker->text(),
            'content' => $this->faker->text(),
            'status' => $this->faker->randomElement(\PulsarLabs\Generators\Tests\TestSupport\Support\PostStatuses::class)->value,
            'published_at' => $this->faker->dateTime(),
            'category_id' => \App\Models\Category::factory()->create(),
        ];
    }
}
