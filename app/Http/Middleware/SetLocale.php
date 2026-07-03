<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Resolves the active locale from a `?lang=` query parameter (persisted to
     * session once valid) or a previously stored session value, falling back to
     * the configured app default when neither is present. No user-facing
     * language switcher exists yet; this middleware is the backend hook a
     * future toggle will link to.
     *
     * Note: this only sets App/Carbon locale for translation strings and
     * Carbon's own locale-aware methods (e.g. diffForHumans). Plain
     * `->format()` calls elsewhere do not localize month/day names — that
     * needs `->translatedFormat()` instead, which is out of scope here.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.available_locales', [config('app.locale')]);

        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, $availableLocales, true)) {
            $request->session()->put('locale', $requested);
        }

        $locale = $request->session()->get('locale');

        if (is_string($locale) && in_array($locale, $availableLocales, true)) {
            App::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
