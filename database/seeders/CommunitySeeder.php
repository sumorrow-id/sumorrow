<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(3)->get();

        if ($users->count() === 0) {
            return;
        }

        // Cycle through whatever users exist so the seeder works even when
        // fewer than three users are present in the database.
        $userId = fn (int $index): string => $users[$index % $users->count()]->id;

        $communities = [
            [
                'name' => 'Summit Space',
                'slug' => 'summit-space-'.Str::random(8),
                'description' => 'A community for serious mountain climbers sharing summiting experiences and techniques.',
                'privacy' => 'public',
                'created_by' => $userId(0),
            ],
            [
                'name' => 'Pendaki Rebahan',
                'slug' => 'pendaki-rebahan-'.Str::random(8),
                'description' => 'Casual climbing enthusiasts who love the outdoors and mountain adventures.',
                'privacy' => 'public',
                'created_by' => $userId(1),
            ],
            [
                'name' => 'HIMIL SQUAD OTW TO PRAU',
                'slug' => 'himil-squad-otw-to-prau-'.Str::random(8),
                'description' => 'A dedicated group organizing climbing expeditions to Mount Prau.',
                'privacy' => 'private',
                'created_by' => $userId(2),
            ],
            [
                'name' => 'Alpine Fluidity',
                'slug' => 'alpine-fluidity-'.Str::random(8),
                'description' => 'Technical climbing community focused on alpine mountaineering and high-altitude expeditions.',
                'privacy' => 'public',
                'created_by' => $userId(0),
            ],
            [
                'name' => 'Rinjani Explorers',
                'slug' => 'rinjani-explorers-'.Str::random(8),
                'description' => 'Community dedicated to exploring Mount Rinjani and its surroundings.',
                'privacy' => 'public',
                'created_by' => $userId(1),
            ],
        ];

        foreach ($communities as $communityData) {
            $community = Community::create($communityData);

            // Add creator as admin
            $community->members()->attach($communityData['created_by'], ['role' => 'admin']);

            // Add other users as members
            foreach ($users as $user) {
                if ($user->id !== $communityData['created_by']) {
                    // Only add to public communities or randomly for private
                    if ($communityData['privacy'] === 'public' || rand(0, 1)) {
                        $community->members()->attach($user->id, ['role' => 'member']);
                    }
                }
            }
        }
    }
}
