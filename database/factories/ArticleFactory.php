<?php

namespace Database\Factories;

use App\Entities\Article\ArticleEntity;
use App\Entities\Category\CategoryEntity;
use App\Entities\Source\SourceEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArticleEntity>
 */
class ArticleFactory extends Factory
{

    protected $model = ArticleEntity::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'author' => fake()->name(),
            'url' => fake()->unique()->url(),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'category_id' => CategoryEntity::factory(),
            'source_id' => SourceEntity::factory(),
        ];
    }
}
