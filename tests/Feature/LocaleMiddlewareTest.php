<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_english_with_no_query_or_session(): void
    {
        $this->get('/home');

        $this->assertSame('en', app()->getLocale());
    }

    public function test_lang_query_param_sets_locale_and_persists_to_session(): void
    {
        $response = $this->get('/home?lang=id');

        $this->assertSame('id', app()->getLocale());
        $this->assertSame('id', session('locale'));
        $response->assertSee('Beranda');
    }

    public function test_locale_persists_across_a_later_request_without_the_query_param(): void
    {
        $this->get('/home?lang=id');
        $this->get('/explore');

        $this->assertSame('id', app()->getLocale());
    }

    public function test_invalid_lang_query_param_is_ignored(): void
    {
        $this->get('/home?lang=xx');

        $this->assertSame('en', app()->getLocale());
        $this->assertNull(session('locale'));
    }

    public function test_default_locale_is_restored_when_a_request_has_no_stored_locale(): void
    {
        $this->get('/home?lang=id');
        session()->forget('locale');

        $this->get('/home');

        $this->assertSame('en', app()->getLocale());
        $this->assertSame('en', Carbon::getLocale());
    }

    public function test_locale_resolves_for_an_authenticated_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard?lang=id');

        $this->assertSame('id', app()->getLocale());
    }

    public function test_indonesian_validation_messages_are_available(): void
    {
        $response = $this->post('/login?lang=id');

        $response->assertSessionHasErrors([
            'email' => 'alamat email wajib diisi.',
            'password' => 'kata sandi wajib diisi.',
        ]);
    }
}
