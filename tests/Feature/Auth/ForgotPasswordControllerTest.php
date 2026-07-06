<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /forgot-password
    // -------------------------------------------------------------------------

    public function test_forgot_password_form_is_displayed(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_authenticated_user_is_redirected_from_forgot_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/forgot-password');

        $response->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // POST /forgot-password — success
    // -------------------------------------------------------------------------

    public function test_reset_link_is_sent_to_existing_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('message');
        $response->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_reset_notification_keeps_the_selected_locale(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password?lang=id', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            fn (ResetPasswordNotification $notification): bool => $notification->locale === 'id'
        );
    }

    // -------------------------------------------------------------------------
    // POST /forgot-password — failures
    // -------------------------------------------------------------------------

    public function test_reset_link_is_not_sent_for_unknown_email(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nobody@example.com',
        ]);

        // Anti-enumeration: an unknown email gets the same generic success
        // response as a known one, with no notification actually sent.
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('message');
        Notification::assertNothingSent();
    }

    public function test_forgot_password_requires_email_field(): void
    {
        $response = $this->post('/forgot-password', []);

        $response->assertSessionHasErrors('email');
    }

    public function test_forgot_password_requires_valid_email_format(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
