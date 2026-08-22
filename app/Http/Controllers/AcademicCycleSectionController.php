<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\ChangeAcademicCycleSectionStatus;
use App\Actions\Curriculum\CreateAcademicCycleSection;
use App\Actions\Curriculum\RollForwardAcademicCycleSections;
use App\Actions\Curriculum\UpdateAcademicCycleSection;
use App\Enums\AcademicStructureStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\ChangeAcademicCycleSectionStatusRequest;
use App\Http\Requests\RollForwardAcademicCycleSectionsRequest;
use App\Http\Requests\StoreAcademicCycleSectionRequest;
use App\Http\Requests\UpdateAcademicCycleSectionRequest;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicCycleSectionController extends Controller
{
    public function __construct(
        private CreateAcademicCycleSection $createAcademicCycleSection,
        private UpdateAcademicCycleSection $updateAcademicCycleSection,
        private ChangeAcademicCycleSectionStatus $changeAcademicCycleSectionStatus,
        private RollForwardAcademicCycleSections $rollForwardAcademicCycleSections,
    ) {
        $this->authorizeResource(AcademicCycleSection::class, 'academicCycleSection');
    }

    public function index(Request $request): View
    {
        $academicYears = $this->academicYears();
        $academicLevels = AcademicLevel::inSchool()->orderBy('position')->orderBy('name')->get(['id', 'name', 'label']);

        $selectedAcademicYearId = $this->selectedAcademicYearId($request, $academicYears);
        $selectedAcademicLevelId = $this->selectedId($request, 'academic_level_id', $academicLevels->modelKeys());
        $selectedStatus = $this->selectedStatus($request);

        $academicCycleSections = $this->filtered($selectedAcademicYearId, $selectedAcademicLevelId, $selectedStatus)
            ->with([
                'academicLevel:id,name,label',
                'academicYear:id,start_year,stop_year',
                'homeroomTeacher:id,name',
            ])
            ->orderByDesc('academic_year_id')
            ->orderBy('academic_level_id')
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $totalCount = AcademicCycleSection::inSchool()->count();

        return view('pages.academic-cycle-section.index', compact(
            'academicCycleSections',
            'academicYears',
            'academicLevels',
            'selectedAcademicYearId',
            'selectedAcademicLevelId',
            'selectedStatus',
            'totalCount',
        ));
    }

    public function create(Request $request): View
    {
        $options = $this->formOptions();
        $preselectedAcademicYearId = $this->selectedId($request, 'academic_year_id', $options['academicYears']->modelKeys())
            ?? current_academic_year_id();
        $preselectedAcademicLevelId = $this->selectedId($request, 'academic_level_id', $options['academicLevels']->modelKeys());

        return view('pages.academic-cycle-section.create', $options + compact(
            'preselectedAcademicYearId',
            'preselectedAcademicLevelId',
        ));
    }

    public function store(StoreAcademicCycleSectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $academicYear = AcademicYear::inSchool()->findOrFail($data['academic_year_id']);
        $academicLevel = AcademicLevel::inSchool()->findOrFail($data['academic_level_id']);

        $section = $this->createAcademicCycleSection->create(
            $academicYear,
            $academicLevel,
            $data['name'],
            $data,
            $this->teacherFrom($data['homeroom_teacher_id'] ?? null),
            $request->user(),
        );

        return redirect()
            ->route('academic-cycle-sections.show', $section)
            ->with('success', 'Cycle section created as a draft. Activate it when the setup is right.');
    }

    public function show(AcademicCycleSection $academicCycleSection): View
    {
        $academicCycleSection->load([
            'academicYear:id,start_year,stop_year,status',
            'academicLevel:id,name,label,code',
            'homeroomTeacher:id,name',
        ]);

        $siblings = AcademicCycleSection::inSchool()
            ->where('academic_level_id', $academicCycleSection->academic_level_id)
            ->where('academic_year_id', $academicCycleSection->academic_year_id)
            ->whereKeyNot($academicCycleSection->id)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'label', 'status']);

        return view('pages.academic-cycle-section.show', compact('academicCycleSection', 'siblings'));
    }

    public function edit(AcademicCycleSection $academicCycleSection): View|RedirectResponse
    {
        $academicCycleSection->load(['academicYear:id,start_year,stop_year,status', 'academicLevel:id,name,label']);

        if (!$academicCycleSection->isEditable()) {
            return redirect()
                ->route('academic-cycle-sections.show', $academicCycleSection)
                ->with('danger', 'This cycle section is archived or its cycle is closed, so its setup cannot change.');
        }

        return view('pages.academic-cycle-section.edit', $this->formOptions() + compact('academicCycleSection'));
    }

    public function update(UpdateAcademicCycleSectionRequest $request, AcademicCycleSection $academicCycleSection): RedirectResponse
    {
        $data = $request->validated();

        $this->updateAcademicCycleSection->update(
            $academicCycleSection,
            $data,
            $this->teacherFrom($data['homeroom_teacher_id'] ?? null),
            $request->user(),
        );

        return redirect()
            ->route('academic-cycle-sections.show', $academicCycleSection)
            ->with('success', 'Cycle section updated.');
    }

    public function changeStatus(ChangeAcademicCycleSectionStatusRequest $request, AcademicCycleSection $academicCycleSection): RedirectResponse
    {
        $status = AcademicStructureStatus::from($request->validated('status'));

        $this->changeAcademicCycleSectionStatus->change($academicCycleSection, $status, $request->user());

        return back()->with('success', "Cycle section is now {$status->label()}.");
    }

    /**
     * Show what a roll-forward would copy before anything is written.
     */
    public function rollForwardForm(Request $request): View
    {
        $this->authorize('create', AcademicCycleSection::class);

        $academicYears = $this->academicYears();
        $source = $this->cycleFrom($request, 'source_academic_year_id', $academicYears);
        $target = $this->cycleFrom($request, 'target_academic_year_id', $academicYears);

        $preview = null;
        $problem = null;

        if ($source !== null && $target !== null) {
            try {
                $preview = $this->rollForwardAcademicCycleSections->preview($source, $target);
            } catch (InvalidValueException $exception) {
                $problem = $exception->getMessage();
            }
        }

        return view('pages.academic-cycle-section.roll-forward', compact('academicYears', 'source', 'target', 'preview', 'problem'));
    }

    public function rollForward(RollForwardAcademicCycleSectionsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $source = AcademicYear::inSchool()->findOrFail($data['source_academic_year_id']);
        $target = AcademicYear::inSchool()->findOrFail($data['target_academic_year_id']);

        $sections = $this->rollForwardAcademicCycleSections->rollForward($source, $target, $request->user());

        if ($sections->isEmpty()) {
            return redirect()
                ->route('academic-cycle-sections.index', ['academic_year_id' => $target->id])
                ->with('success', "{$target->name} already has every section of {$source->name}. Nothing was copied.");
        }

        return redirect()
            ->route('academic-cycle-sections.index', ['academic_year_id' => $target->id])
            ->with('success', "{$sections->count()} draft cycle sections were created in {$target->name}. Learners, teachers, and timetables did not come along.");
    }

    /**
     * @return Builder<AcademicCycleSection>
     */
    private function filtered(?int $academicYearId, ?int $academicLevelId, ?AcademicStructureStatus $status): Builder
    {
        return AcademicCycleSection::inSchool()
            ->when($academicYearId !== null, fn (Builder $query) => $query->where('academic_year_id', $academicYearId))
            ->when($academicLevelId !== null, fn (Builder $query) => $query->where('academic_level_id', $academicLevelId))
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status));
    }

    /**
     * Read the records the create and edit forms can choose from.
     *
     * @return array{academicYears: Collection<int, AcademicYear>, academicLevels: Collection<int, AcademicLevel>, teachers: Collection<int, User>}
     */
    private function formOptions(): array
    {
        $academicYears = $this->academicYears();
        $academicLevels = AcademicLevel::inSchool()
            ->where('status', AcademicStructureStatus::Active)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'label']);

        $teachers = User::ofSchool()->role(Role::Teacher->value)->orderBy('name')->get(['users.id', 'users.name']);

        return compact('academicYears', 'academicLevels', 'teachers');
    }

    /**
     * @return Collection<int, AcademicYear>
     */
    private function academicYears(): Collection
    {
        return AcademicYear::inSchool()->orderByDesc('start_year')->get(['id', 'start_year', 'stop_year', 'status']);
    }

    /**
     * Default the cycle filter to the cycle being worked in.
     *
     * An explicit empty value asks for every cycle, so a person can always see
     * the whole history without clearing the address bar.
     *
     * @param Collection<int, AcademicYear> $academicYears
     */
    private function selectedAcademicYearId(Request $request, Collection $academicYears): ?int
    {
        if (!$request->has('academic_year_id')) {
            $current = current_academic_year_id();

            return in_array($current, $academicYears->modelKeys(), true) ? $current : null;
        }

        return $this->selectedId($request, 'academic_year_id', $academicYears->modelKeys());
    }

    /**
     * @param array<int, int> $allowed
     */
    private function selectedId(Request $request, string $key, array $allowed): ?int
    {
        $value = $request->query($key);

        if (!is_string($value) || $value === '' || !ctype_digit($value)) {
            return null;
        }

        return in_array((int) $value, $allowed, true) ? (int) $value : null;
    }

    private function selectedStatus(Request $request): ?AcademicStructureStatus
    {
        $status = $request->query('status');

        return is_string($status) ? AcademicStructureStatus::tryFrom($status) : null;
    }

    /**
     * @param Collection<int, AcademicYear> $academicYears
     */
    private function cycleFrom(Request $request, string $key, Collection $academicYears): ?AcademicYear
    {
        $id = $this->selectedId($request, $key, $academicYears->modelKeys());

        return $id === null ? null : $academicYears->firstWhere('id', $id);
    }

    private function teacherFrom(int|string|null $teacherId): ?User
    {
        return $teacherId === null ? null : User::query()->findOrFail($teacherId);
    }
}
