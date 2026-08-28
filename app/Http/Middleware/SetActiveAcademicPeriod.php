<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\School\SchoolContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Work out which academic year and academic period the request works in.
 *
 * The school must be resolved first, because a period always belongs to one
 * school. A person keeps their own working period for the school and year.
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

        if ($school !== null && $request->user() instanceof User) {
            $this->periodContext->resolveFor($school, $request->user(), $request);
        }

        return $next($request);
    }
}
