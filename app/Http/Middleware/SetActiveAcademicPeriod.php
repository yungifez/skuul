<?php

namespace App\Http\Middleware;

use App\Services\Academic\AcademicPeriodContext;
use App\Services\School\SchoolContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Work out which academic year and semester the request works in.
 *
 * The school must be resolved first, because a period always belongs to one
 * school. A person keeps their own working period in the session.
 */
class SetActiveAcademicPeriod
{
    public function __construct(
        private SchoolContext $schoolContext,
        private AcademicPeriodContext $periodContext,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $school = $this->schoolContext->school();

        if ($school !== null) {
            $this->periodContext->resolveFor($school, $request);
        }

        return $next($request);
    }
}
