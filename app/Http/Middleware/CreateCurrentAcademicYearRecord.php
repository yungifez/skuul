<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateCurrentAcademicYearRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()?->user()?->studentRecord != null && !auth()->user()?->studentRecord?->academicYears()->find(current_school()->academic_year_id)) {
            auth()->user()->studentRecord->academicYears()->syncWithoutDetaching([
                current_school()->academicYear->id => [
                    'my_class_id' => auth()->user()->studentRecord->my_class_id,
                    'section_id' => auth()->user()->studentRecord->section_id,
                ],
            ]);
        }

        return $next($request);
    }
}
