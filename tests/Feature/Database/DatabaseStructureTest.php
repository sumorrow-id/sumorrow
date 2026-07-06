<?php

namespace Tests\Feature\Database;

use App\Models\Achievement;
use App\Models\Community;
use App\Models\Gear;
use App\Models\Mountain;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_community_foreign_keys_cascade_on_user_deletion(): void
    {
        $creator = User::factory()->create();
        $member = User::factory()->create();
        $community = Community::create([
            'name' => 'Database Test Community',
            'slug' => 'database-test-community',
            'description' => 'Tests database constraints.',
            'privacy' => 'public',
            'created_by' => $creator->id,
        ]);
        $community->members()->attach($member->id);

        $member->delete();
        $this->assertDatabaseMissing('community_user', ['community_id' => $community->id, 'user_id' => $member->id]);

        $creator->delete();
        $this->assertModelMissing($community);
    }

    public function test_community_names_are_unique(): void
    {
        $creator = User::factory()->create();
        Community::create([
            'name' => 'Unique Community',
            'slug' => 'unique-community',
            'description' => 'First.',
            'privacy' => 'public',
            'created_by' => $creator->id,
        ]);

        $this->expectException(QueryException::class);

        Community::create([
            'name' => 'Unique Community',
            'slug' => 'different-slug',
            'description' => 'Duplicate.',
            'privacy' => 'public',
            'created_by' => $creator->id,
        ]);
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed();

        $counts = [
            User::class => User::count(),
            Mountain::class => Mountain::count(),
            Achievement::class => Achievement::count(),
            Gear::class => Gear::count(),
            Community::class => Community::count(),
        ];

        $this->seed();

        foreach ($counts as $model => $count) {
            $this->assertSame($count, $model::count());
        }

        $this->assertNotNull(Community::where('privacy', 'private')->firstOrFail()->join_token);
    }
}
