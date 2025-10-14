<?php

namespace Database\Seeders;

use App\Entities\Source\SourceEntity;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [ 'newsapi', 'nytimes', 'guardian'];

        foreach ($sources as $source) {
            SourceEntity::updateOrCreate(['name' => $source]);
        }
    }
}
