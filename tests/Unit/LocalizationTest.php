<?php

namespace Tests\Unit;

use App\Http\Middleware\SetLocale;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_locale_is_reset_and_valid_selection_is_persisted(): void
    {
        $session = new Store('test', new ArraySessionHandler(120));
        $session->start();
        $request = Request::create('/home');
        $request->setLaravelSession($session);

        App::setLocale('id');
        Carbon::setLocale('id');

        (new SetLocale)->handle($request, fn (): Response => new Response);

        $this->assertSame('en', app()->getLocale());
        $this->assertSame('en', Carbon::getLocale());

        $request = Request::create('/home', 'GET', ['lang' => 'id']);
        $request->setLaravelSession($session);

        (new SetLocale)->handle($request, fn (): Response => new Response);

        $this->assertSame('id', app()->getLocale());
        $this->assertSame('id', Carbon::getLocale());
        $this->assertSame('id', $session->get('locale'));
        $this->assertSame('Batas (15 kg)', __('gear.limit', ['weight' => 15]));
    }

    public function test_translation_files_have_matching_keys_placeholders_and_tags(): void
    {
        $englishFiles = collect(glob(lang_path('en/*.php')))->map('basename')->sort()->values();
        $indonesianFiles = collect(glob(lang_path('id/*.php')))->map('basename')->sort()->values();

        $this->assertSame($englishFiles->all(), $indonesianFiles->all());

        foreach ($englishFiles as $file) {
            $english = Arr::dot(require lang_path("en/{$file}"));
            $indonesian = Arr::dot(require lang_path("id/{$file}"));

            $this->assertSame(array_keys($english), array_keys($indonesian), "Translation keys differ in {$file}.");

            foreach ($english as $key => $value) {
                if (! is_string($value)) {
                    $this->assertSame($value, $indonesian[$key]);

                    continue;
                }

                preg_match_all('/:[A-Za-z_][A-Za-z0-9_]*|\{\{.*?\}\}|\{[A-Za-z_][A-Za-z0-9_]*\}|<\/?[A-Za-z][^>]*>/', $value, $englishTokens);
                preg_match_all('/:[A-Za-z_][A-Za-z0-9_]*|\{\{.*?\}\}|\{[A-Za-z_][A-Za-z0-9_]*\}|<\/?[A-Za-z][^>]*>/', $indonesian[$key], $indonesianTokens);

                sort($englishTokens[0]);
                sort($indonesianTokens[0]);

                $this->assertSame($englishTokens[0], $indonesianTokens[0], "Tokens differ for {$file}:{$key}.");
            }
        }

        $english = json_decode(file_get_contents(lang_path('en.json')), true, flags: JSON_THROW_ON_ERROR);
        $indonesian = json_decode(file_get_contents(lang_path('id.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(array_keys($english), array_keys($indonesian));

        foreach ($english as $key => $value) {
            preg_match_all('/:[A-Za-z_][A-Za-z0-9_]*/', $value, $englishTokens);
            preg_match_all('/:[A-Za-z_][A-Za-z0-9_]*/', $indonesian[$key], $indonesianTokens);

            $this->assertSame($englishTokens[0], $indonesianTokens[0], "Tokens differ for JSON key: {$key}.");
        }
    }

    public function test_indonesian_validation_messages_are_available(): void
    {
        App::setLocale('id');

        $errors = Validator::make([], [
            'email' => ['required'],
            'password' => ['required'],
        ])->errors();

        $this->assertSame('alamat email wajib diisi.', $errors->first('email'));
        $this->assertSame('kata sandi wajib diisi.', $errors->first('password'));
    }

    public function test_standard_laravel_translation_keys_are_available(): void
    {
        $keys = [
            'auth.failed',
            'auth.password',
            'auth.throttle',
            'passwords.reset',
            'passwords.sent',
            'passwords.throttled',
            'passwords.token',
            'passwords.user',
        ];

        foreach (['en', 'id'] as $locale) {
            foreach ($keys as $key) {
                $this->assertNotSame($key, __($key, locale: $locale));
            }
        }
    }

    public function test_password_reset_notification_keeps_the_selected_locale(): void
    {
        Notification::fake();
        App::setLocale('id');

        $user = new User([
            'username' => 'Pendaki',
            'email' => 'pendaki@example.com',
        ]);

        $user->sendPasswordResetNotification('token');

        Notification::assertSentTo(
            $user,
            fn (ResetPasswordNotification $notification): bool => $notification->locale === 'id'
        );
    }

    public function test_email_verification_notification_keeps_the_selected_locale(): void
    {
        Notification::fake();
        App::setLocale('id');

        $user = new User([
            'username' => 'Pendaki',
            'email' => 'pendaki@example.com',
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            $user,
            fn (VerifyEmailNotification $notification): bool => $notification->locale === 'id'
        );
    }
}
