<?php

namespace App\Http\Middleware;

use App\Models\Installation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SetApplicationLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) config('app.locale');

        try {
            $installedLocale = Installation::withoutGlobalScopes()->value('locale');

            if (is_string($installedLocale) && $installedLocale !== '') {
                $locale = $installedLocale;
            }
        } catch (Throwable) {
            // The installer must still render before the application schema exists.
        }

        if (!array_key_exists($locale, config('app.supported_locales', []))) {
            $locale = (string) config('app.fallback_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
