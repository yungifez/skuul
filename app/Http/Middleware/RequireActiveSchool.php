<?php

namespace App\Http\Middleware;

use App\Services\School\SchoolContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stop a request that needs a school but does not have one.
 *
 * A person with no school access cannot reach school data at all. A person who
 * has access but has not chosen a school is sent to choose one.
 */
class RequireActiveSchool
{
    public function __construct(private SchoolContext $schoolContext) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->schoolContext->has()) {
            return $next($request);
        }

        $user = $request->user();

        if ($user !== null && !$user->isPlatformAdmin() && !$user->schoolMemberships()->active()->exists()) {
            abort(403, "You do not have access to any school. Contact your school's administrator.");
        }

        session()->flash('danger', 'Please set your working school first.');

        return redirect()->route('schools.index');
    }
}
