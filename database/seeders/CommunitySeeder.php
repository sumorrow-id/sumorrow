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
                'description' => 'A community for serious mountain climbers sharing summiting experiences and techniques.',
                'privacy' => 'public',
                'created_by' => $userId(0),
            ],
            [
                'name' => 'Pendaki Rebahan',
                'description' => 'Casual climbing enthusiasts who love the outdoors and mountain adventures.',
                'privacy' => 'public',
                'created_by' => $userId(1),
            ],
            [
                'name' => 'HIMIL SQUAD OTW TO PRAU',
                'description' => 'A dedicated group organizing climbing expeditions to Mount Prau.',
                'privacy' => 'private',
                'created_by' => $userId(2),
            ],
            [
                'name' => 'Alpine Fluidity',
                'description' => 'Technical climbing community focused on alpine mountaineering and high-altitude expeditions.',
                'privacy' => 'public',
                'created_by' => $userId(0),
            ],
            [
                'name' => 'Rinjani Explorers',
                'description' => 'Community dedicated to exploring Mount Rinjani and its surroundings.',
                'privacy' => 'public',
                'created_by' => $userId(1),
            ],
        ];

        foreach ($communities as $communityData) {
            $slug = Str::slug($communityData['name']);
            $community = Community::updateOrCreate(
                ['name' => $communityData['name']],
                [...$communityData, 'slug' => $slug],
            );

            if ($community->privacy === 'private' && ! $community->join_token) {
                $community->update(['join_token' => Str::upper(Str::random(8))]);
            }

            $community->members()->syncWithoutDetaching([
                $communityData['created_by'] => ['role' => 'admin'],
            ]);

            if ($community->privacy === 'public') {
                foreach ($users as $user) {
                    if ($user->id !== $communityData['created_by']) {
                        $community->members()->syncWithoutDetaching([
                            $user->id => ['role' => 'member'],
                        ]);
                    }
                }
            }
        }
    }
}
