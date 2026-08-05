<?php

namespace Tests\Unit\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Services\AchievementService;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;

    private AchievementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AchievementSeeder::class);
        $this->service = new AchievementService;
    }

    private function hasFirstSummit(User $user): bool
    {
        return $user->achievements()->where('title', 'First Summit')->exists();
    }

    public function test_first_summit_unlocks_after_posting_a_summit_log(): void
    {
        $user = User::factory()->create();
        // Summit logs carry no category tags.
        $user->posts()->create(['title' => 'Summit Semeru', 'body' => 'Made it to Mahameru.']);

        $this->service->checkAndUnlockAchievements($user);

        $this->assertTrue($this->hasFirstSummit($user));
    }

    public function test_first_summit_stays_locked_without_any_post(): void
    {
        $user = User::factory()->create();

        $this->service->checkAndUnlockAchievements($user);

        $this->assertFalse($this->hasFirstSummit($user));
    }

    public function test_a_forum_post_alone_does_not_unlock_first_summit(): void
    {
        $user = User::factory()->create();
        $forumPost = $user->posts()->create(['title' => '', 'body' => 'Just chatting.']);
        $forumPost->tags()->create(['keyword' => 'Hiking Stories']);

        $this->service->checkAndUnlockAchievements($user);

        $this->assertFalse($this->hasFirstSummit($user));
    }

    public function test_unlocking_is_idempotent(): void
    {
        $user = User::factory()->create();
        $user->posts()->create(['title' => 'Summit Semeru', 'body' => 'Made it.']);

        $this->service->checkAndUnlockAchievements($user);
        $this->service->checkAndUnlockAchievements($user);

        $this->assertSame(1, $user->achievements()->where('title', 'First Summit')->count());
    }

    public function test_descriptions_are_translated_when_the_locale_changes(): void
    {
        $achievement = Achievement::where('title', 'First Summit')->firstOrFail();

        $this->assertSame(__('achievements.first_summit', [], 'en'), $achievement->localizedDescription());

        $this->app->setLocale('id');

        $this->assertSame(__('achievements.first_summit', [], 'id'), $achievement->localizedDescription());
        $this->assertNotSame($achievement->description, $achievement->localizedDescription());
    }
}
