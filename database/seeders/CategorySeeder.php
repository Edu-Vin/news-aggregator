<?php

namespace Database\Seeders;

use App\Entities\Category\CategoryEntity;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Health',
            'Science',
            'Technology',
            'Politics',
            'Entertainment',
            'Sports',
            'Style',
            'Fashion',
            'General'
        ];

        foreach ($categories as $category) {
            CategoryEntity::updateOrCreate(['name' => $category]);
        }
    }
}
