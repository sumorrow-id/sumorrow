<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function province(string $name = 'Jawa Tengah'): Province
    {
        return Province::create(['name' => $name]);
    }

    private function mountain(Province $province, array $overrides = []): Mountain
    {
        return Mountain::create(array_merge([
            'province_id' => $province->id,
            'name' => 'Test Mountain',
            'elevation_masl' => 3000,
            'length_km' => 10.0,
            'elevation_gain_m' => 1500,
            'coordinates' => '7 deg 27\' 0" S, 110 deg 26\' 24" E',
            'description' => 'A test mountain description.',
            'is_active' => true,
            'difficulty' => 'moderate',
            'avg_rating' => 4.0,
        ], $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    private function validMountainPayload(Province $province, array $overrides = []): array
    {
        return array_merge([
            'province_id' => $province->id,
            'name' => 'Gunung Baru',
            'elevation_masl' => 2500,
            'length_km' => 8.5,
            'elevation_gain_m' => 1200,
            'coordinates' => '7 deg 6\' 0" S, 110 deg 12\' 0" E',
            'description' => 'A brand new mountain.',
            'difficulty' => 'moderate',
            'is_active' => '1',
        ], $overrides);
    }

    /**
     * A real 1x1 GIF upload — UploadedFile::fake()->image() needs the GD
     * extension, which not every dev machine or CI runner has enabled.
     */
    private function fakeImageUpload(string $name = 'cover.gif'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($path, base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'));

        return new UploadedFile($path, $name, 'image/gif', null, true);
    }

    // -------------------------------------------------------------------------
    // Auth gate — guests
    // -------------------------------------------------------------------------

    public function test_guest_is_redirected_to_login_for_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_user_updates(): void
    {
        $this->get(route('admin.user-updates'))->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_mountain_data(): void
    {
        $this->get(route('admin.mountain-data'))->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // Role gate — authenticated non-admin
    // -------------------------------------------------------------------------

    public function test_non_admin_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Unauthorized access.');
    }

    public function test_non_admin_user_cannot_access_user_updates(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.user-updates'))
            ->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_mountain_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.mountain-data'))
            ->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_delete_users(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_non_admin_user_cannot_manage_mountains(): void
    {
        $user = User::factory()->create();
        $province = $this->province();

        $this->actingAs($user)
            ->post(route('admin.mountains.store'), $this->validMountainPayload($province))
            ->assertRedirect('/');

        $this->assertDatabaseMissing('mountains', ['name' => 'Gunung Baru']);
    }

    // -------------------------------------------------------------------------
    // Admin containment — admins only see admin pages
    // -------------------------------------------------------------------------

    public function test_admin_is_redirected_from_home_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('home'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_is_redirected_from_root_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_is_redirected_from_explore_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('explore'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_is_redirected_from_community_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('community'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_is_redirected_from_profile_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('profile'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_logged_in_admin_visiting_login_is_redirected_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('showLogin'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_logged_in_admin_visiting_google_redirect_is_sent_to_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('google.redirect'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_still_logout(): void
    {
        $response = $this->actingAs($this->admin())->post(route('logout'));

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_regular_user_can_still_access_home(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function test_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas(['totalUsers', 'newUsersCount', 'totalMountains', 'activeMountains', 'recentUsers']);
    }

    public function test_dashboard_counts_new_users_within_last_seven_days(): void
    {
        $admin = $this->admin();

        // Fresh user — within 7 days.
        User::factory()->create();
        // Old user — created 10 days ago, should NOT count.
        User::factory()->create()->forceFill(['created_at' => now()->subDays(10)])->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // admin + the fresh user = 2, the old user excluded.
        $response->assertViewHas('newUsersCount', 2);
    }

    public function test_dashboard_counts_mountains(): void
    {
        $admin = $this->admin();
        $province = $this->province();
        $this->mountain($province, ['name' => 'A', 'is_active' => true]);
        $this->mountain($province, ['name' => 'B', 'is_active' => false]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('totalMountains', 2);
        $response->assertViewHas('activeMountains', 1);
    }

    // -------------------------------------------------------------------------
    // User updates
    // -------------------------------------------------------------------------

    public function test_admin_can_view_user_updates(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.user-updates'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-updates');
        $response->assertViewHas(['totalUsers', 'newThisWeek', 'newThisMonth', 'recentUsers']);
    }

    public function test_user_updates_total_users_includes_admin(): void
    {
        $admin = $this->admin();
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.user-updates'));

        $response->assertViewHas('totalUsers', 4);
    }

    public function test_user_updates_search_filters_by_username_or_email(): void
    {
        $admin = $this->admin();
        User::factory()->create(['username' => 'sherpa_tenzing', 'email' => 'tenzing@example.com']);
        User::factory()->create(['username' => 'flatlander', 'email' => 'flat@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.user-updates', ['q' => 'sherpa']));

        $users = $response->viewData('recentUsers');
        $this->assertCount(1, $users);
        $this->assertSame('sherpa_tenzing', $users->first()->username);
    }

    // -------------------------------------------------------------------------
    // User management
    // -------------------------------------------------------------------------

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.role', $target), ['role' => 'admin']);

        $response->assertSessionHas('success');
        $this->assertSame('admin', $target->fresh()->role);
    }

    public function test_admin_can_demote_another_admin_to_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.users.role', $target), ['role' => 'user']);

        $this->assertSame('user', $target->fresh()->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->patch(route('admin.users.role', $admin), ['role' => 'user']);

        $response->assertSessionHas('error');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_role_update_rejects_invalid_role(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.role', $target), ['role' => 'superuser']);

        $response->assertSessionHasErrors('role');
        $this->assertSame('user', $target->fresh()->role);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $target));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_export_users_as_csv(): void
    {
        $admin = $this->admin();
        User::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.users.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('Username,Email,Role', $response->streamedContent());
    }

    // -------------------------------------------------------------------------
    // Mountain data
    // -------------------------------------------------------------------------

    public function test_admin_can_view_mountain_data(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.mountain-data'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.mountain-data');
        $response->assertViewHas(['totalMountains', 'activeMountains', 'challengingRoutes', 'mountains']);
    }

    public function test_mountain_data_counts_active_mountains_only(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->mountain($province, ['name' => 'A', 'is_active' => true]);
        $this->mountain($province, ['name' => 'B', 'is_active' => true]);
        $this->mountain($province, ['name' => 'C', 'is_active' => false]);

        $response = $this->actingAs($admin)->get(route('admin.mountain-data'));

        $response->assertViewHas('totalMountains', 3);
        $response->assertViewHas('activeMountains', 2);
    }

    public function test_mountain_data_counts_only_hard_and_strenuous_as_challenging(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->mountain($province, ['name' => 'Easy peak', 'difficulty' => 'easy']);
        $this->mountain($province, ['name' => 'Moderate peak', 'difficulty' => 'moderate']);
        $this->mountain($province, ['name' => 'Hard peak', 'difficulty' => 'hard']);
        $this->mountain($province, ['name' => 'Brutal peak', 'difficulty' => 'strenuous']);

        $response = $this->actingAs($admin)->get(route('admin.mountain-data'));

        $response->assertViewHas('challengingRoutes', 2);
    }

    public function test_mountain_data_search_filters_by_name_or_province(): void
    {
        $admin = $this->admin();
        $jateng = $this->province('Jawa Tengah');
        $jabar = $this->province('Jawa Barat');

        $this->mountain($jateng, ['name' => 'Merbabu']);
        $this->mountain($jabar, ['name' => 'Ciremai']);

        $byName = $this->actingAs($admin)->get(route('admin.mountain-data', ['q' => 'merb']));
        $this->assertCount(1, $byName->viewData('mountains'));
        $this->assertSame('Merbabu', $byName->viewData('mountains')->first()->name);

        $byProvince = $this->actingAs($admin)->get(route('admin.mountain-data', ['q' => 'Jawa Barat']));
        $this->assertCount(1, $byProvince->viewData('mountains'));
        $this->assertSame('Ciremai', $byProvince->viewData('mountains')->first()->name);
    }

    // -------------------------------------------------------------------------
    // Mountain management
    // -------------------------------------------------------------------------

    public function test_admin_can_view_create_mountain_form(): void
    {
        $this->province();

        $response = $this->actingAs($this->admin())->get(route('admin.mountains.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.mountains.create');
        $response->assertViewHas('provinces');
    }

    public function test_admin_can_create_mountain(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $response = $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province));

        $response->assertRedirect(route('admin.mountain-data'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('mountains', [
            'name' => 'Gunung Baru',
            'province_id' => $province->id,
            'is_active' => true,
        ]);
    }

    public function test_mountain_creation_requires_valid_data(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $response = $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'name' => '',
            'difficulty' => 'impossible',
            'elevation_masl' => -5,
        ]));

        $response->assertSessionHasErrors(['name', 'difficulty', 'elevation_masl']);
        $this->assertDatabaseCount('mountains', 0);
    }

    public function test_mountain_creation_rejects_non_dms_coordinates(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        foreach (['7.45S 110.44E', '-7.45, 110.44', 'Semarang', '8 deg 16\' 0" S'] as $invalid) {
            $response = $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
                'coordinates' => $invalid,
            ]));

            $response->assertSessionHasErrors(['coordinates']);
        }

        $this->assertDatabaseCount('mountains', 0);
    }

    public function test_mountain_creation_accepts_dms_coordinates_parseable_by_weather(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'coordinates' => '8 deg 16\' 0" S, 115 deg 25\' 30.5" E',
        ]));

        $this->assertDatabaseHas('mountains', ['coordinates' => '8 deg 16\' 0" S, 115 deg 25\' 30.5" E']);
    }

    public function test_admin_can_create_mountain_with_cover_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $province = $this->province();

        $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'image' => $this->fakeImageUpload(),
        ]));

        $mountain = Mountain::where('name', 'Gunung Baru')->firstOrFail();
        $image = $mountain->images()->firstOrFail();
        $this->assertTrue($image->is_cover);
        $this->assertSame(1, $image->position);
        Storage::disk('public')->assertExists($image->getRawOriginal('image_url'));
    }

    public function test_mountain_creation_rejects_non_image_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $province = $this->province();

        $response = $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]));

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseCount('mountains', 0);
    }

    public function test_updating_cover_image_replaces_the_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $province = $this->province();
        $mountain = $this->mountain($province);
        $oldPath = $this->fakeImageUpload('old.gif')->store('mountains', 'public');
        $mountain->images()->create(['image_url' => $oldPath, 'position' => 1, 'is_cover' => true]);

        $this->actingAs($admin)->put(route('admin.mountains.update', $mountain), $this->validMountainPayload($province, [
            'image' => $this->fakeImageUpload('new.gif'),
        ]));

        $this->assertSame(1, $mountain->images()->count());
        $newPath = $mountain->images()->first()->getRawOriginal('image_url');
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_creating_open_mountain_clears_closed_since(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'is_active' => '1',
            'closed_since' => '2026-01-01',
        ]));

        $this->assertDatabaseHas('mountains', [
            'name' => 'Gunung Baru',
            'closed_since' => null,
        ]);
    }

    public function test_admin_can_view_edit_mountain_form(): void
    {
        $province = $this->province();
        $mountain = $this->mountain($province);

        $response = $this->actingAs($this->admin())->get(route('admin.mountains.edit', $mountain));

        $response->assertStatus(200);
        $response->assertViewIs('admin.mountains.edit');
        $response->assertViewHas(['mountain', 'provinces']);
    }

    public function test_admin_can_update_mountain(): void
    {
        $admin = $this->admin();
        $province = $this->province();
        $mountain = $this->mountain($province);

        $response = $this->actingAs($admin)->put(route('admin.mountains.update', $mountain), $this->validMountainPayload($province, [
            'name' => 'Renamed Peak',
            'is_active' => '0',
            'closed_since' => '2026-06-01',
        ]));

        $response->assertRedirect(route('admin.mountain-data'));
        $mountain->refresh();
        $this->assertSame('Renamed Peak', $mountain->name);
        $this->assertFalse($mountain->is_active);
        $this->assertSame('2026-06-01', $mountain->closed_since->format('Y-m-d'));
    }

    public function test_admin_can_create_mountain_with_basecamps(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'basecamps' => ['Basecamp Kalisat', '  Basecamp Ranu Pani  ', '', 'Basecamp Kalisat'],
        ]));

        $mountain = Mountain::where('name', 'Gunung Baru')->firstOrFail();
        $this->assertSame(
            ['Basecamp Kalisat', 'Basecamp Ranu Pani'],
            $mountain->basecamps()->orderBy('name')->pluck('name')->all()
        );
    }

    public function test_updating_basecamps_adds_removes_and_keeps_ids(): void
    {
        $admin = $this->admin();
        $province = $this->province();
        $mountain = $this->mountain($province);
        $kept = $mountain->basecamps()->create(['name' => 'Basecamp Kalisat']);
        $mountain->basecamps()->create(['name' => 'Basecamp Lama']);

        $this->actingAs($admin)->put(route('admin.mountains.update', $mountain), $this->validMountainPayload($province, [
            'basecamps' => ['Basecamp Kalisat', 'Basecamp Baru'],
        ]));

        $this->assertSame(
            ['Basecamp Baru', 'Basecamp Kalisat'],
            $mountain->basecamps()->orderBy('name')->pluck('name')->all()
        );
        $this->assertSame($kept->id, $mountain->basecamps()->where('name', 'Basecamp Kalisat')->value('id'));
    }

    public function test_submitting_no_basecamps_clears_them(): void
    {
        $admin = $this->admin();
        $province = $this->province();
        $mountain = $this->mountain($province);
        $mountain->basecamps()->create(['name' => 'Basecamp Lama']);

        $this->actingAs($admin)->put(route('admin.mountains.update', $mountain), $this->validMountainPayload($province, [
            'basecamps' => [''],
        ]));

        $this->assertSame(0, $mountain->basecamps()->count());
    }

    public function test_basecamp_name_is_length_limited(): void
    {
        $admin = $this->admin();
        $province = $this->province();

        $response = $this->actingAs($admin)->post(route('admin.mountains.store'), $this->validMountainPayload($province, [
            'basecamps' => [str_repeat('a', 256)],
        ]));

        $response->assertSessionHasErrors(['basecamps.0']);
        $this->assertDatabaseCount('mountains', 0);
    }

    public function test_edit_form_prefills_existing_basecamps(): void
    {
        $province = $this->province();
        $mountain = $this->mountain($province);
        $mountain->basecamps()->create(['name' => 'Basecamp Kalisat']);

        $response = $this->actingAs($this->admin())->get(route('admin.mountains.edit', $mountain));

        $response->assertSee('Basecamp Kalisat', false);
    }

    public function test_admin_can_delete_mountain(): void
    {
        $admin = $this->admin();
        $province = $this->province();
        $mountain = $this->mountain($province);

        $response = $this->actingAs($admin)->delete(route('admin.mountains.destroy', $mountain));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('mountains', ['id' => $mountain->id]);
    }
}
