<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureAcademicPeriodIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (current_academic_period_id() == null) {
            session()->flash('danger', 'Please set the academic period before proceeding.');

            $academicYear = current_academic_year();

            return $academicYear === null
                ? redirect()->route('academic-years.index')
                : redirect()->route('academic-years.show', $academicYear);
        }

        return $next($request);
    }
}
