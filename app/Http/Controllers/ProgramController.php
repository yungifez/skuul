<?php

namespace App\Http\Controllers;

use App\Actions\Cohort\ChangeProgramParticipation;
use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreProgramParticipationRequest;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramParticipationRequest;
use App\Models\Program;
use App\Models\ProgramParticipation;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * A named activity a student takes part in.
 *
 * Taking part never touches enrollment. A student who leaves a club is still
 * a student.
 */
class ProgramController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private ChangeProgramParticipation $changeParticipation) {}

    /**
     * Show the programmes this school runs.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Program::class);

        $selectedType = ProgramType::tryFrom($request->string('type')->toString());
        $activeOnly = $request->boolean('active');

        $programs = Program::query()
            ->inSchool()
            // The relation closure gets a plain builder, so the condition of
            // ProgramParticipation::scopeRunning() is written out here.
            ->withCount(['participations as running_count' => function (Builder $query): void {
                $query->whereIn('status', [ParticipationStatus::Requested, ParticipationStatus::Active]);
            }])
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->when($activeOnly, function (Builder $query): void {
                $query->active();
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.program.index', [
            'programs' => $programs,
            'types' => ProgramType::cases(),
            'selectedType' => $selectedType,
            'activeOnly' => $activeOnly,
        ]);
    }

    /**
     * Show the form that opens a programme.
     */
    public function create(): View
    {
        $this->authorize('create', Program::class);

        return view('pages.program.create', ['types' => ProgramType::cases()]);
    }

    /**
     * Open a programme.
     */
    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $program = Program::create([
            'school_id' => current_school_id(),
            ...$request->validated(),
        ]);

        return redirect()->route('programs.show', $program)->with('success', 'The programme was opened.');
    }

    /**
     * Show one programme and who takes part.
     */
    public function show(Program $program): View
    {
        $this->authorize('view', $program);

        $program->load([
            'participations.studentRecord.user:id,name',
            'participations.staff:id,name',
        ]);

        return view('pages.program.show', [
            'program' => $program,
            'students' => $this->schoolLearners(),
            'staff' => $this->schoolStaff(),
            'statuses' => ParticipationStatus::cases(),
        ]);
    }

    /**
     * Give a learner a place.
     */
    public function storeParticipation(StoreProgramParticipationRequest $request, Program $program): RedirectResponse
    {
        $enrollment = StudentRecord::query()->inSchool()->findOrFail($request->integer('student_record_id'));

        try {
            $this->changeParticipation->join(
                program: $program,
                enrollment: $enrollment,
                startsOn: $request->string('starts_on')->toString() ?: null,
                staff: $request->filled('staff_id') ? User::findOrFail($request->integer('staff_id')) : null,
                schedule: $request->string('schedule')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['participation' => $exception->getMessage()]);
        }

        return back()->with('success', 'The learner has a place.');
    }

    /**
     * Move a place to another state.
     */
    public function updateParticipation(
        UpdateProgramParticipationRequest $request,
        Program $program,
        ProgramParticipation $programParticipation,
    ): RedirectResponse {
        abort_unless($programParticipation->program_id === $program->id, 404);

        try {
            $this->changeParticipation->changeStatus(
                participation: $programParticipation,
                status: ParticipationStatus::from($request->string('status')->toString()),
                note: $request->string('note')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['participation' => $exception->getMessage()]);
        }

        return back()->with('success', 'The place is now '.$programParticipation->fresh()->status->label().'.');
    }
}
