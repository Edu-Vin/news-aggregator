<?php

namespace Database\Seeders;

use App\Entities\Category\CategoryEntity;
use App\Entities\Source\SourceEntity;
use App\Entities\User\UserEntity;
use App\Entities\User\UserPreferenceEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = UserEntity::with(['preference'])->get();
        $sources = SourceEntity::all();
        $categories = CategoryEntity::all();
        $authors = ['John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Williams'];

        foreach ($users as $user) {
            UserPreferenceEntity::create([
                'user_id' => $user->id,
                'sources' => $sources->random(rand(1, 3))->pluck('id')->toArray(),
                'categories' => $categories->random(rand(1, 3))->pluck('id')->toArray(),
                'authors' => $authors,
            ]);
        }
    }
}
