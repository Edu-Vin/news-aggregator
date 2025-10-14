<?php

namespace Database\Factories;

use App\Entities\Category\CategoryEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryEntity>
 */
class CategoryFactory extends Factory
{

    protected $model = CategoryEntity::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
