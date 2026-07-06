<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
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

        // Throttle credential submissions (keyed by email + IP) to slow brute-force attempts.
        RateLimiter::for('login', function (Request $request) {
            $identifier = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($identifier);
        });
    }
}
