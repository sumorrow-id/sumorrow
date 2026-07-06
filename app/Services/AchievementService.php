<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\PostImage;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Memeriksa dan membuka (unlock) achievement untuk user berdasarkan action yang sudah dilakukan.
     */
    public function checkAndUnlockAchievements(User $user): void
    {
        $achievements = Achievement::all();
        $unlockedIds = $user->achievements()->pluck('achievements.id')->toArray(); // 'achievements.id' to avoid ambiguity just in case
        $newUnlocks = [];

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $unlockedIds)) {
                continue;
            }

            $unlocked = false;
            switch ($achievement->title) {
                case 'First Summit':
                    // Successfully logged your first mountain climbing activity
                    $unlocked = $user->posts()->exists();
                    break;

                case 'The Explorer':
                    // Successfully conquered 5 different mountains.
                    $unlocked = $user->posts()->distinct('mountain_id')->count('mountain_id') >= 5;
                    break;

                case 'Altitude Junkie':
                    // Conquered 3 mountains above 3.000 MASL.
                    $unlocked = $user->posts()
                        ->whereHas('mountain', function ($q) {
                            $q->where('elevation_masl', '>', 3000);
                        })
                        ->distinct('mountain_id')
                        ->count('mountain_id') >= 3;
                    break;

                case 'Endurance Master':
                    // Completed a grueling expedition lasting 3 days or more.
                    $unlocked = $user->posts()->where('duration_days', '>=', 3)->exists();
                    break;

                case 'Local Guide':
                    // Wrote 5 detailed mountain reviews to help the community.
                    $unlocked = $user->ratings()->count() >= 5;
                    break;

                case 'Visual Storyteller':
                    // Shared 10 or more stunning photos of your climbing journeys.
                    $unlocked = PostImage::whereHas('post', function ($q) use ($user) {
                        $q->where('author_id', $user->id);
                    })->count() >= 10;
                    break;

                case 'Prepared Hiker':
                    // Logged at least 10 essential items in your personal gear list.
                    $unlocked = $user->gears()->count() >= 10;
                    break;
            }

            if ($unlocked) {
                // Attach the timestamp
                $newUnlocks[$achievement->id] = ['unlocked_at' => Carbon::now()];
            }
        }

        if (! empty($newUnlocks)) {
            $user->achievements()->attach($newUnlocks);
        }
    }
}
