<?php

namespace Database\Seeders;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            return;
        }

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
                $gear,
            );
        }
    }
}
