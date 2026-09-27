<?php

namespace App\Http\Controllers;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\SupportCategory;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreSupportPlanRequest;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Open a plan of help, run it, and close it.
 *
 * A health or counselling plan is readable only by the people who run it, so
 * every list here goes through the plan's own readable-by rule as well as the
 * policy.
 */
class SupportPlanController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private ManageSupportPlan $manageSupportPlan) {}

    /**
     * Show the plans this person may read.
     */
    public function index(): View
    {
        $this->authorize('viewAny', SupportPlan::class);

        return view('pages.support-plan.index');
    }

    /**
     * Show the form that opens a plan.
     */
    public function create(): View
    {
        $this->authorize('create', SupportPlan::class);

        return view('pages.support-plan.create', [
            'categories' => SupportCategory::cases(),
            'students' => $this->schoolLearners(),
            'staff' => $this->schoolStaff(),
        ]);
    }

    /**
     * Open a plan.
     */
    public function store(StoreSupportPlanRequest $request): RedirectResponse
    {
        $enrollment = StudentRecord::query()->inSchool()->findOrFail($request->integer('student_record_id'));

        try {
            $plan = $this->manageSupportPlan->open(
                enrollment: $enrollment,
                title: $request->string('title')->toString(),
                category: SupportCategory::from($request->string('category')->toString()),
                summary: $request->string('summary')->toString() ?: null,
                startsOn: $request->string('starts_on')->toString() ?: null,
                reviewOn: $request->string('review_on')->toString() ?: null,
                owner: $request->filled('assigned_to') ? User::findOrFail($request->integer('assigned_to')) : null,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['support_plan' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('support-plans.show', $plan)->with('success', 'The plan was opened.');
    }

    /**
     * Show one plan with everything recorded against it.
     */
    public function show(SupportPlan $supportPlan): View
    {
        $this->authorize('view', $supportPlan);

        return view('pages.support-plan.show', ['plan' => $supportPlan]);
    }
}
