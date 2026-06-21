<?php

namespace App\Providers;

use App\Contracts\SocialAuthInterface;
use App\Services\GoogleAuthService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SocialAuthInterface::class, GoogleAuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(30)->by($request->user()->getAuthIdentifier())
                : Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('weather', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
