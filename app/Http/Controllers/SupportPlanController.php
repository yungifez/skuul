<?php

namespace App\Http\Controllers;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreSupportPlanActionRequest;
use App\Http\Requests\StoreSupportPlanNoteRequest;
use App\Http\Requests\StoreSupportPlanRequest;
use App\Http\Requests\UpdateSupportPlanStatusRequest;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\SupportPlanAction;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportPlan::class);

        $selectedStatus = SupportPlanStatus::tryFrom($request->string('status')->toString());
        $selectedCategory = SupportCategory::tryFrom($request->string('category')->toString());
        $dueOnly = $request->boolean('due');

        $readable = fn (): Builder => SupportPlan::query()->inSchool()->readableBy($request->user());

        $plans = $readable()
            ->with(['studentRecord.user:id,name', 'assignedTo:id,name'])
            ->withCount(['actions', 'notes'])
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedCategory !== null, function (Builder $query) use ($selectedCategory): void {
                $query->where('category', $selectedCategory);
            })
            ->when($dueOnly, function (Builder $query): void {
                $query->dueForReview();
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.support-plan.index', [
            'plans' => $plans,
            'statuses' => SupportPlanStatus::cases(),
            'categories' => SupportCategory::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory,
            'dueOnly' => $dueOnly,
            'openCount' => $readable()->open()->count(),
            'dueCount' => $readable()->dueForReview()->count(),
        ]);
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

        $supportPlan->load([
            'studentRecord.user:id,name',
            'actions.assignedTo:id,name',
            'notes.writtenBy:id,name',
            'statusChanges.changedBy:id,name',
            'createdBy:id,name',
            'assignedTo:id,name',
        ]);

        return view('pages.support-plan.show', [
            'plan' => $supportPlan,
            'nextStatuses' => $supportPlan->status->allowedNext(),
            'staff' => $this->schoolStaff(),
        ]);
    }

    /**
     * Move the plan to another state.
     */
    public function changeStatus(UpdateSupportPlanStatusRequest $request, SupportPlan $supportPlan): RedirectResponse
    {
        try {
            $this->manageSupportPlan->changeStatus(
                plan: $supportPlan,
                status: SupportPlanStatus::from($request->string('status')->toString()),
                actor: $request->user(),
                reason: $request->string('reason')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('success', 'The plan moved to '.$supportPlan->fresh()->status->label().'.');
    }

    /**
     * Add a step the school agrees to take.
     */
    public function storeAction(StoreSupportPlanActionRequest $request, SupportPlan $supportPlan): RedirectResponse
    {
        try {
            $this->manageSupportPlan->addAction(
                plan: $supportPlan,
                description: $request->string('description')->toString(),
                dueOn: $request->string('due_on')->toString() ?: null,
                assignee: $request->filled('assigned_to') ? User::findOrFail($request->integer('assigned_to')) : null,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['action' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'The step was added to the plan.');
    }

    /**
     * Record that a step is done.
     */
    public function completeAction(SupportPlan $supportPlan, SupportPlanAction $supportPlanAction): RedirectResponse
    {
        $this->authorize('update', $supportPlan);

        abort_unless($supportPlanAction->support_plan_id === $supportPlan->id, 404);

        $this->manageSupportPlan->completeAction($supportPlanAction, request()->user());

        return back()->with('success', 'The step was marked done.');
    }

    /**
     * Write a note about how the plan is going.
     */
    public function storeNote(StoreSupportPlanNoteRequest $request, SupportPlan $supportPlan): RedirectResponse
    {
        try {
            $this->manageSupportPlan->addNote(
                plan: $supportPlan,
                body: $request->string('body')->toString(),
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['note' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'The note was added to the plan.');
    }
}
