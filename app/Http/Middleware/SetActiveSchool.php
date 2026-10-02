<?php

namespace App\Http\Middleware;

use App\Services\School\SchoolContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Work out which school the request works in, and set it once.
 *
 * Every later query, policy, role check, and permission check reads the school
 * from this context, so no other code needs to remember a school condition.
 */
class SetActiveSchool
{
    public function __construct(private SchoolContext $schoolContext) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $this->schoolContext->resolveFor($user, $request);
        }

        return $next($request);
    }
}
