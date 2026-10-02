<?php

namespace App\Http\Middleware;

use App\Enums\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block the routes of a feature the school turned off.
 *
 * The records stay. Only the way in closes, so a school can turn a feature
 * back on and find its history where it left it.
 */
class EnsureFeatureIsEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $case = Feature::tryFrom($feature);

        abort_if($case === null, 500, "There is no feature called $feature.");
        abort_unless(feature_enabled($case), 404);

        return $next($request);
    }
}
