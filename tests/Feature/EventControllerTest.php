<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeCommunity(array $overrides = []): Community
    {
        return Community::create(array_merge([
            'name' => 'Pendaki Nusantara',
            'slug' => 'pendaki-nusantara',
            'description' => 'A community for Indonesian hikers.',
            'privacy' => 'public',
            'image_url' => null,
            'created_by' => User::factory()->create()->id,
        ], $overrides));
    }

    /**
     * @return array<string, string>
     */
    private function validEventPayload(): array
    {
        return [
            'title' => 'Sunrise Hike',
            'event_date' => now()->addWeek()->format('Y-m-d H:i'),
            'location' => 'Basecamp Selo',
            'description' => 'Meet at the basecamp at dawn.',
        ];
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_member_can_create_an_event(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->post(
            route('community.events.store', $community),
            $this->validEventPayload()
        );

        $response->assertRedirect(route('community.show', [$community, 'tab' => 'events']));
        $this->assertDatabaseHas('events', [
            'community_id' => $community->id,
            'user_id' => $member->id,
            'title' => 'Sunrise Hike',
        ]);
    }

    public function test_member_can_create_an_event_with_an_image(): void
    {
        Storage::fake('public');

        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $this->actingAs($member)->post(
            route('community.events.store', $community),
            $this->validEventPayload() + [
                'image' => UploadedFile::fake()->create('event.jpg', 100, 'image/jpeg'),
            ]
        )->assertSessionHasNoErrors();

        $event = Event::firstOrFail();
        $this->assertStringStartsWith('storage/events/', $event->image_url);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $event->image_url));
    }

    public function test_non_member_cannot_create_an_event(): void
    {
        $user = User::factory()->create();
        $community = $this->makeCommunity();

        $response = $this->actingAs($user)->post(
            route('community.events.store', $community),
            $this->validEventPayload()
        );

        $response->assertForbidden();
        $this->assertDatabaseCount('events', 0);
    }

    public function test_guest_cannot_create_an_event(): void
    {
        $community = $this->makeCommunity();

        $response = $this->post(route('community.events.store', $community), $this->validEventPayload());

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('events', 0);
    }

    public function test_event_date_must_be_in_the_future(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->post(
            route('community.events.store', $community),
            array_merge($this->validEventPayload(), ['event_date' => now()->subDay()->format('Y-m-d H:i')])
        );

        $response->assertSessionHasErrorsIn('event', 'event_date');
        $this->assertDatabaseCount('events', 0);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    private function makeEvent(Community $community, User $creator): Event
    {
        return $community->events()->create([
            'user_id' => $creator->id,
            'title' => 'Sunrise Hike',
            'description' => 'Meet at the basecamp at dawn.',
            'event_date' => now()->addWeek(),
            'location' => 'Basecamp Selo',
        ]);
    }

    public function test_event_creator_can_delete_their_event(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $event = $this->makeEvent($community, $member);

        $response = $this->actingAs($member)->delete(route('community.events.destroy', $event));

        $response->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_community_owner_can_delete_any_event(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $community = $this->makeCommunity(['created_by' => $owner->id]);
        $community->members()->attach($member->id, ['role' => 'member']);
        $event = $this->makeEvent($community, $member);

        $response = $this->actingAs($owner)->delete(route('community.events.destroy', $event));

        $response->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_other_member_cannot_delete_someone_elses_event(): void
    {
        $creator = User::factory()->create();
        $other = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($creator->id, ['role' => 'member']);
        $community->members()->attach($other->id, ['role' => 'member']);
        $event = $this->makeEvent($community, $creator);

        $response = $this->actingAs($other)->delete(route('community.events.destroy', $event));

        $response->assertForbidden();
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }

    // -------------------------------------------------------------------------
    // listing
    // -------------------------------------------------------------------------

    public function test_events_are_listed_on_the_community_page(): void
    {
        $member = User::factory()->create();
        $community = $this->makeCommunity();
        $community->members()->attach($member->id, ['role' => 'member']);
        $this->makeEvent($community, $member);

        $response = $this->actingAs($member)->get(route('community.show', $community));

        $response->assertOk();
        $response->assertSee('Sunrise Hike');
    }
}
