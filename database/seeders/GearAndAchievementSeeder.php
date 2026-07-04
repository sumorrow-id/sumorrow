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
        // ==========================================
        // Pilar Jalur Ekspedisi
        // ==========================================
        $firstClimb = Achievement::updateOrCreate(
            ['title' => 'First Summit'],
            ['description' => 'Successfully logged your first mountain climbing activity.', 'icon_url' => '⛰️']
        );

        $explorer = Achievement::updateOrCreate(
            ['title' => 'The Explorer'],
            ['description' => 'Successfully conquered 5 different mountains.', 'icon_url' => '🗺️']
        );

        $altitudeJunkie = Achievement::updateOrCreate(
            ['title' => 'Altitude Junkie'],
            ['description' => 'Conquered 3 mountains above 3.000 MASL.', 'icon_url' => '☁️']
        );

        $enduranceMaster = Achievement::updateOrCreate(
            ['title' => 'Endurance Master'],
            ['description' => 'Completed a grueling expedition lasting 3 days or more.', 'icon_url' => '🏕️']
        );

        // ==========================================
        // Pilar Komunitas
        // ==========================================
        $localGuide = Achievement::updateOrCreate(
            ['title' => 'Local Guide'],
            ['description' => 'Wrote 5 detailed mountain reviews to help the community.', 'icon_url' => '✍️']
        );

        $visualStoryteller = Achievement::updateOrCreate(
            ['title' => 'Visual Storyteller'],
            ['description' => 'Shared 10 or more stunning photos of your climbing journeys.', 'icon_url' => '📸']
        );

        // ==========================================
        // Pilar Persiapan
        // ==========================================
        $preparedHiker = Achievement::updateOrCreate(
            ['title' => 'Prepared Hiker'],
            ['description' => 'Logged at least 10 essential items in your personal gear list.', 'icon_url' => '🎒']
        );

        $user = User::first();
        if (! $user) {
            return;
        }

        // ------------------------------------------
        // Attach ke User
        // ------------------------------------------
        $user->achievements()->syncWithoutDetaching([
            $firstClimb->id => ['unlocked_at' => Carbon::now()->subMonths(5)],
            $explorer->id => ['unlocked_at' => Carbon::now()->subMonths(2)],
            $preparedHiker->id => ['unlocked_at' => Carbon::now()->subWeeks(3)],
        ]);

        // ------------------------------------------
        // Seed Gear
        // ------------------------------------------
        $gears = [
            ['name' => 'Atmos AG 65', 'brand' => 'Osprey', 'weight_grams' => 2040, 'category' => 'Backpack'],
            ['name' => 'Elixir 2', 'brand' => 'MSR', 'weight_grams' => 2770, 'category' => 'Tent'],
            ['name' => 'ThermoBall Eco', 'brand' => 'The North Face', 'weight_grams' => 450, 'category' => 'Apparel'],
            ['name' => 'Targhee III Mid WP', 'brand' => 'Keen', 'weight_grams' => 490, 'category' => 'Footwear'],
            ['name' => 'Spot 400 Headlamp', 'brand' => 'Black Diamond', 'weight_grams' => 78, 'category' => 'Navigation & Light'],
            ['name' => 'PocketRocket 2', 'brand' => 'MSR', 'weight_grams' => 73, 'category' => 'Cooking & Hydration'],
            ['name' => 'Ultralight Medical Kit', 'brand' => 'Adventure Medical', 'weight_grams' => 220, 'category' => 'First Aid'],
        ];

        foreach ($gears as $gear) {
            Gear::updateOrCreate(
                ['user_id' => $user->id, 'name' => $gear['name']],
                [
                    'brand' => $gear['brand'],
                    'weight_grams' => $gear['weight_grams'],
                    'category' => $gear['category'],
                ]
            );
        }
    }
}
