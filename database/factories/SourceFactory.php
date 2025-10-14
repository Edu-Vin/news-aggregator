<?php

namespace Database\Factories;

use App\Entities\Source\SourceEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SourceEntity>
 */
class SourceFactory extends Factory
{
    protected $model = SourceEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
        ];
    }
}
