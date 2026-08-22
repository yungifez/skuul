<?php

namespace App\Http\Controllers;

use App\Actions\Cohort\ChangeCohortMembership;
use App\Enums\CohortType;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreCohortMemberRequest;
use App\Http\Requests\StoreCohortRequest;
use App\Http\Requests\UpdateCohortRequest;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\StudentRecord;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * A named group of people that is not a class and not a section.
 *
 * A place in a group is kept, not deleted, so a school can still see who was
 * in a group last year.
 */
class CohortController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private ChangeCohortMembership $changeMembership) {}

    /**
     * Show the groups this person may read.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Cohort::class);

        $selectedType = CohortType::tryFrom($request->string('type')->toString());
        $activeOnly = $request->boolean('active');

        $cohorts = Cohort::query()
            ->inSchool()
            // The relation closure gets a plain builder, so the condition of
            // CohortMember::scopeCurrent() is written out here.
            ->withCount(['members as current_members_count' => function (Builder $query): void {
                $query->whereNull('left_on');
            }])
            ->when(!$request->user()->can('read restricted cohort'), function (Builder $query): void {
                $query->where('is_restricted', false);
            })
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->when($activeOnly, function (Builder $query): void {
                $query->active();
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.cohort.index', [
            'cohorts' => $cohorts,
            'types' => CohortType::cases(),
            'selectedType' => $selectedType,
            'activeOnly' => $activeOnly,
        ]);
    }

    /**
     * Show the form that makes a group.
     */
    public function create(): View
    {
        $this->authorize('create', Cohort::class);

        return view('pages.cohort.create', ['types' => CohortType::cases()]);
    }

    /**
     * Make a group.
     */
    public function store(StoreCohortRequest $request): RedirectResponse
    {
        $cohort = Cohort::create([
            'school_id' => current_school_id(),
            'academic_year_id' => current_academic_year_id(),
            'created_by' => $request->user()->id,
            ...$request->validated(),
        ]);

        return redirect()->route('cohorts.show', $cohort)->with('success', 'The group was made.');
    }

    /**
     * Show one group and who is in it.
     */
    public function show(Cohort $cohort): View
    {
        $this->authorize('view', $cohort);

        $cohort->load([
            'members.studentRecord.user:id,name',
            'members.user:id,name',
        ]);

        return view('pages.cohort.show', [
            'cohort' => $cohort,
            'students' => $this->schoolLearners(),
        ]);
    }

    /**
     * Rename a group, or close it.
     */
    public function update(UpdateCohortRequest $request, Cohort $cohort): RedirectResponse
    {
        $cohort->update($request->validated());

        return back()->with('success', 'The group was saved.');
    }

    /**
     * Put a learner into the group.
     */
    public function storeMember(StoreCohortMemberRequest $request, Cohort $cohort): RedirectResponse
    {
        $enrollment = StudentRecord::query()->inSchool()->findOrFail($request->integer('student_record_id'));

        try {
            $this->changeMembership->addStudent(
                cohort: $cohort,
                enrollment: $enrollment,
                joinedOn: $request->string('joined_on')->toString() ?: null,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['member' => $exception->getMessage()]);
        }

        return back()->with('success', 'The learner joined the group.');
    }

    /**
     * Take somebody out of the group, keeping the place they held.
     */
    public function removeMember(Cohort $cohort, CohortMember $cohortMember): RedirectResponse
    {
        $this->authorize('update', $cohort);

        abort_unless($cohortMember->cohort_id === $cohort->id, 404);

        $this->changeMembership->remove($cohortMember);

        return back()->with('success', 'The place was closed. The group still shows who held it.');
    }
}
