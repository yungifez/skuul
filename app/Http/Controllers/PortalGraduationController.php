<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\CohortMember;
use App\Models\GraduationPlan;
use App\Models\StudentRecord;
use App\Services\Graduation\GraduationProgress;
use App\Services\Portal\PortalAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a learner and their guardian the progress the school has published.
 */
class PortalGraduationController extends Controller
{
    public function __construct(
        private PortalAccess $access,
        private GraduationProgress $progress,
    ) {}

    /**
     * Read the active plans that apply to this enrollment.
     */
    public function show(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Graduation, $studentRecord->school_id), 404);

        $cohortIds = CohortMember::query()
            ->where('student_record_id', $studentRecord->id)
            ->whereNull('left_on')
            ->whereHas('cohort', fn (Builder $query) => $query->where('school_id', $studentRecord->school_id))
            ->pluck('cohort_id');

        $plans = GraduationPlan::query()
            ->inSchool($studentRecord->school_id)
            ->active()
            ->whereNull('parent_id')
            ->where(function (Builder $query) use ($cohortIds): void {
                $query->whereNull('cohort_id')->orWhereIn('cohort_id', $cohortIds);
            })
            ->with('cohort:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (GraduationPlan $plan): array => [
                'plan' => $plan,
                'progress' => $this->progress->for($plan, $studentRecord),
            ]);

        return view('pages.portal.graduation', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'plans' => $plans,
        ]);
    }
}
