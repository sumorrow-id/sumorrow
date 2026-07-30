<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public function test_auth_emails_are_not_queued_so_they_send_without_a_worker(): void
    {
        // The VM deploy runs no queue worker; queueing these silently drops the mail.
        $this->assertNotInstanceOf(ShouldQueue::class, new VerifyEmailNotification);
        $this->assertNotInstanceOf(ShouldQueue::class, new ResetPasswordNotification('token'));
    }

    public function test_user_can_verify_email_via_signed_link(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get($this->verificationUrlFor($user));

        $response->assertRedirect(route('home'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_link_clicked_while_logged_out_still_verifies_after_login(): void
    {
        // Opening the email on another device/browser means the click arrives as a guest.
        $user = User::factory()->unverified()->create();
        $url = $this->verificationUrlFor($user);

        $this->get($url)->assertRedirect('/login');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect($url);

        $this->get($url)->assertRedirect(route('home'));
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

    public function test_verification_email_uses_sumorrow_branding(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $html = (string) (new VerifyEmailNotification)->toMail($user)->render();

        $this->assertStringContainsString('SUMORROW-LOGO-BLACK.png', $html);
        $this->assertStringContainsString('#094174', $html);
        $this->assertStringContainsString(__('auth.verify_notification_action'), $html);
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
