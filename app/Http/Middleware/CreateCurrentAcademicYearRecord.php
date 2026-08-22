<?php

namespace App\Http\Middleware;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Give a student a placement record for the academic year being worked in.
 *
 * A student keeps one placement record for each academic year. This creates
 * the record for the current year the first time the student is seen in it.
 */
class CreateCurrentAcademicYearRecord
{
    public function __construct(private ChangeEnrollmentPlacement $changeEnrollmentPlacement)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $enrollment = $user instanceof User ? $user->studentRecord : null;
        $academicYear = current_academic_year();

        if (
            $enrollment !== null
            && $academicYear?->isOpen()
            && !$enrollment->status->isClosed()
            && $enrollment->academicCycleSection !== null
            && !$enrollment->placements()->where('academic_year_id', $academicYear->id)->exists()
        ) {
            $this->changeEnrollmentPlacement->place(
                enrollment: $enrollment,
                academicCycleSection: $enrollment->academicCycleSection,
                academicPeriod: current_academic_period(),
                actor: $user,
                reason: 'Academic year backfill',
            );
        }

        return $next($request);
    }
}
