<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGraduationExemptionRequest;
use App\Http\Requests\StoreGraduationPlanRequest;
use App\Http\Requests\StoreGraduationRequirementRequest;
use App\Http\Requests\UpdateGraduationPlanRequest;
use App\Models\Cohort;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Graduation\GraduationProgress;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * What a learner must finish before the school will let them graduate.
 *
 * Only a published result counts towards a plan. Work still in the gradebook
 * is not a result, so a plan never reads a mark a family has not seen.
 */
class GraduationPlanController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private GraduationProgress $progress) {}

    /**
     * Show the plans this school keeps.
     */
    public function index(): View
    {
        $this->authorize('viewAny', GraduationPlan::class);

        return view('pages.graduation-plan.index', [
            'plans' => GraduationPlan::query()
                ->inSchool()
                ->with('cohort:id,name')
                ->withCount('requirements')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    /**
     * Show the form that writes a plan.
     */
    public function create(): View
    {
        $this->authorize('create', GraduationPlan::class);

        return view('pages.graduation-plan.create', ['cohorts' => $this->cohorts()]);
    }

    /**
     * Write a plan.
     */
    public function store(StoreGraduationPlanRequest $request): RedirectResponse
    {
        $plan = GraduationPlan::create([
            'school_id' => current_school_id(),
            ...$request->validated(),
        ]);

        return redirect()->route('graduation-plans.show', $plan)->with('success', 'The plan was written.');
    }

    /**
     * Show one plan, and how far one learner is through it.
     */
    public function show(Request $request, GraduationPlan $graduationPlan): View
    {
        $this->authorize('view', $graduationPlan);

        $graduationPlan->load(['requirements.subject:id,name', 'cohort:id,name']);

        $learner = $this->learnerFrom($request);
        $progress = $learner === null ? null : $this->progress->for($graduationPlan, $learner);

        return view('pages.graduation-plan.show', [
            'plan' => $graduationPlan,
            'subjects' => Subject::query()->inSchool()->orderBy('name')->get(['id', 'name']),
            'students' => $this->schoolLearners(),
            'learner' => $learner,
            'progress' => $progress,
            'exemptions' => $learner === null ? collect() : GraduationExemption::query()
                ->where('student_record_id', $learner->id)
                ->whereIn('graduation_requirement_id', $graduationPlan->requirements->pluck('id'))
                ->get()
                ->keyBy('graduation_requirement_id'),
        ]);
    }

    /**
     * Change a plan.
     */
    public function update(UpdateGraduationPlanRequest $request, GraduationPlan $graduationPlan): RedirectResponse
    {
        $graduationPlan->update($request->validated());

        return back()->with('success', 'The plan was saved.');
    }

    /**
     * Add something a learner must finish.
     */
    public function storeRequirement(StoreGraduationRequirementRequest $request, GraduationPlan $graduationPlan): RedirectResponse
    {
        GraduationRequirement::create([
            'graduation_plan_id' => $graduationPlan->id,
            ...$request->validated(),
        ]);

        return back()->with('success', 'The requirement was added to the plan.');
    }

    /**
     * Take a requirement off the plan.
     */
    public function destroyRequirement(GraduationPlan $graduationPlan, GraduationRequirement $graduationRequirement): RedirectResponse
    {
        $this->authorize('update', $graduationPlan);

        abort_unless($graduationRequirement->graduation_plan_id === $graduationPlan->id, 404);

        $graduationRequirement->delete();

        return back()->with('success', 'The requirement was removed from the plan.');
    }

    /**
     * Excuse one learner from one requirement.
     */
    public function storeExemption(StoreGraduationExemptionRequest $request, GraduationPlan $graduationPlan): RedirectResponse
    {
        GraduationExemption::updateOrCreate(
            [
                'graduation_requirement_id' => $request->integer('graduation_requirement_id'),
                'student_record_id' => $request->integer('student_record_id'),
            ],
            [
                'reason' => $request->string('reason')->toString(),
                'granted_by' => $request->user()->id,
            ],
        );

        return back()->with('success', 'The learner was excused from that requirement.');
    }

    /**
     * Take an excusal back.
     */
    public function destroyExemption(GraduationPlan $graduationPlan, GraduationExemption $graduationExemption): RedirectResponse
    {
        $this->authorize('update', $graduationPlan);

        abort_unless(
            $graduationPlan->requirements()->whereKey($graduationExemption->graduation_requirement_id)->exists(),
            404,
        );

        $graduationExemption->delete();

        return back()->with('success', 'The excusal was taken back.');
    }

    /**
     * Read the learner the screen was asked about.
     */
    private function learnerFrom(Request $request): ?StudentRecord
    {
        $id = $request->integer('student_record_id') ?: null;

        return $id === null
            ? null
            : StudentRecord::query()->inSchool()->with('user:id,name')->find($id);
    }

    /**
     * Get the groups a plan can be written for.
     *
     * @return Collection<int, Cohort>
     */
    private function cohorts(): Collection
    {
        return Cohort::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']);
    }
}
