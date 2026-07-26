<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            User::factory()->count(max(0, 3 - User::count()))->create();
        }

        $this->call([
            AdminSeeder::class,
            MountainSeeder::class,
            AchievementSeeder::class,
            GearSeeder::class,
            CommunitySeeder::class,
        ]);
    }
}
