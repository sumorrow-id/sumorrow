<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function verificationUrlFor(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }

    public function test_registration_sends_exactly_one_verification_email_and_user_starts_unverified(): void
    {
        Notification::fake();

        $this->post(route('register.submit'), [
            'username' => 'newhiker',
            'email' => 'newhiker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'newhiker@example.com')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);
    }

    public function test_user_can_verify_email_via_signed_link(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get($this->verificationUrlFor($user));

        $response->assertRedirect(route('home'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_cannot_be_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong@example.com')]
        );

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_google_users_are_auto_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'google_id' => '1234567890',
            'password_hash' => null,
        ]);

        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_edit_profile_shows_verified_badge_only_after_verification(): void
    {
        $unverified = User::factory()->create(['email_verified_at' => null]);
        $verified = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($unverified)->get(route('profile.edit'))
            ->assertSee(__('profile.email_unverified'))
            ->assertDontSee(__('profile.verified'));

        $this->actingAs($verified)->get(route('profile.edit'))
            ->assertSee(__('profile.verified'))
            ->assertDontSee(__('profile.email_unverified'));
    }
}
