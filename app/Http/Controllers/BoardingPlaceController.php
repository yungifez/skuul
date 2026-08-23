<?php

namespace App\Http\Controllers;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Http\Requests\StoreBoardingPlaceRequest;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\StudentRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Who sleeps in which bed.
 */
class BoardingPlaceController extends Controller
{
    /**
     * Give a learner a bed.
     */
    public function store(StoreBoardingPlaceRequest $request, AssignBoardingPlace $assign): RedirectResponse
    {
        $bed = DormitoryBed::inSchool()->findOrFail($request->validated('dormitory_bed_id'));
        $enrollment = StudentRecord::inSchool()->findOrFail($request->validated('student_record_id'));

        $this->authorize('update', $this->houseOf($bed));

        $assign->assign(
            enrollment: $enrollment,
            bed: $bed,
            reason: $request->validated('reason'),
            effectiveOn: $request->validated('effective_on') === null
                ? null
                : now()->parse($request->validated('effective_on')),
        );

        return back()->with('success', 'The learner has a bed.');
    }

    /**
     * Record that a learner has stopped boarding.
     */
    public function destroy(StudentRecord $studentRecord, Request $request, AssignBoardingPlace $assign): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|min:3|max:255']);

        abort_unless($studentRecord->school_id === current_school_id(), 403);
        abort_unless(auth()->user()?->can('manage boarding') === true, 403);

        $assign->end($studentRecord, $request->string('reason')->toString());

        return back()->with('success', 'The learner has left the house.');
    }

    /**
     * Get the house a bed belongs to, so the policy has something to judge.
     */
    private function houseOf(DormitoryBed $bed): Dormitory
    {
        $dormitory = $bed->loadMissing('room.dormitory')->room?->dormitory;

        abort_if($dormitory === null, 404);

        return $dormitory;
    }
}
