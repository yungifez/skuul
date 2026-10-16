<?php

namespace App\Http\Middleware;

use App\Services\School\DomainContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolve domain branding for guests and signed-in users before school selection.
 * SetActiveSchool still checks campus membership.
 */
class ResolveDomainContext
{
    public function __construct(private DomainContext $domainContext) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('install.*')) {
            return $next($request);
        }

        $this->domainContext->resolveFor($request);

        return $next($request);
    }
}
