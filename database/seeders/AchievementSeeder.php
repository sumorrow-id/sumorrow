<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            ['title' => 'First Summit', 'description' => 'Successfully logged your first mountain climbing activity.', 'icon_url' => '⛰️'],
            ['title' => 'The Explorer', 'description' => 'Successfully conquered 5 different mountains.', 'icon_url' => '🗺️'],
            ['title' => 'Altitude Junkie', 'description' => 'Conquered 3 mountains above 3.000 MASL.', 'icon_url' => '☁️'],
            ['title' => 'Endurance Master', 'description' => 'Completed a grueling expedition lasting 3 days or more.', 'icon_url' => '🏕️'],
            ['title' => 'Local Guide', 'description' => 'Wrote 5 detailed mountain reviews to help the community.', 'icon_url' => '✍️'],
            ['title' => 'Visual Storyteller', 'description' => 'Shared 10 or more stunning photos of your climbing journeys.', 'icon_url' => '📸'],
            ['title' => 'Prepared Hiker', 'description' => 'Logged at least 10 essential items in your personal gear list.', 'icon_url' => '🎒'],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(['title' => $achievement['title']], $achievement);
        }
    }
}
