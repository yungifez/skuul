<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureAcademicYearIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (current_academic_year() === null && !$this->hasExplicitAcademicYear($request)) {
            return redirect()->route('academic-years.index');
        }

        return $next($request);
    }

    private function hasExplicitAcademicYear(Request $request): bool
    {
        if (!$request->routeIs('course-offerings.create', 'course-offerings.store')) {
            return false;
        }

        $academicYearId = $request->input('academic_year_id');

        return is_numeric($academicYearId)
            && AcademicYear::inSchool()->whereKey((int) $academicYearId)->exists();
    }
}
