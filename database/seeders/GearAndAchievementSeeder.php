<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class GearAndAchievementSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (! $user) {
            return;
        }

        // Seed Achievements
        $firstClimb = Achievement::updateOrCreate(
            ['title' => 'First Climb'],
            ['description' => 'Successfully logged your first climbing activity.', 'tier' => 'bronze']
        );

        $experienced = Achievement::updateOrCreate(
            ['title' => 'Experienced Hiker'],
            ['description' => 'Completed more than 5 different mountains.', 'tier' => 'silver']
        );

        $summitHunter = Achievement::updateOrCreate(
            ['title' => 'Summit Hunter'],
            ['description' => 'Conquered 3 mountains above 3000 MASL.', 'tier' => 'gold']
        );

        // Attach to user
        $user->achievements()->syncWithoutDetaching([
            $firstClimb->id => ['unlocked_at' => Carbon::now()->subMonths(5)],
            $experienced->id => ['unlocked_at' => Carbon::now()->subMonths(2)],
        ]);

        // Seed Gear
        Gear::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Atmos AG 65'],
            ['brand' => 'Osprey', 'weight_grams' => 2040, 'category' => 'Backpack']
        );

        Gear::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Elixir 2'],
            ['brand' => 'MSR', 'weight_grams' => 2770, 'category' => 'Tent']
        );

        Gear::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'ThermoBall Eco'],
            ['brand' => 'The North Face', 'weight_grams' => 450, 'category' => 'Apparel']
        );

        Gear::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Targhee III Mid WP'],
            ['brand' => 'Keen', 'weight_grams' => 490, 'category' => 'Footwear']
        );
    }
}
