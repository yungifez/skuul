<?php

namespace App\Http\Middleware;

use App\Services\School\DomainContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read the address the request came in on, before anything else runs.
 *
 * The address is looked up for signed-in people and visitors alike, because
 * the sign-in page itself needs to know whose school it is showing. What the
 * address never does is grant anything: `SetActiveSchool` still checks
 * membership before it opens a campus.
 */
class ResolveDomainContext
{
    public function __construct(private DomainContext $domainContext) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->domainContext->resolveFor($request);

        return $next($request);
    }
}
