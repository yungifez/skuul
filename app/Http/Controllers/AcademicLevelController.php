<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\ChangeAcademicLevelStatus;
use App\Actions\Curriculum\CreateAcademicLevel;
use App\Actions\Curriculum\UpdateAcademicLevel;
use App\Enums\AcademicStructureStatus;
use App\Http\Requests\ChangeAcademicLevelStatusRequest;
use App\Http\Requests\StoreAcademicLevelRequest;
use App\Http\Requests\UpdateAcademicLevelRequest;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicLevelController extends Controller
{
    public function __construct(
        private CreateAcademicLevel $createAcademicLevel,
        private UpdateAcademicLevel $updateAcademicLevel,
        private ChangeAcademicLevelStatus $changeAcademicLevelStatus,
    ) {
        $this->authorizeResource(AcademicLevel::class, 'academicLevel');
    }

    public function index(Request $request): View
    {
        $status = $this->readStatus($request);

        $academicLevels = AcademicLevel::inSchool()
            ->with(['parent:id,name'])
            ->withCount([
                'cycleSections',
                'cycleSections as active_cycle_sections_count' => fn (Builder $query) => $query->where('status', AcademicStructureStatus::Active),
            ])
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $totalCount = AcademicLevel::inSchool()->count();

        return view('pages.academic-level.index', compact('academicLevels', 'status', 'totalCount'));
    }

    public function create(Request $request): View
    {
        $preselectedParent = $request->filled('parent_id')
            ? AcademicLevel::inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->find($request->integer('parent_id'))
            : null;

        return view('pages.academic-level.create', $this->formOptions() + compact('preselectedParent'));
    }

    public function store(StoreAcademicLevelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $academicLevel = $this->createAcademicLevel->create(
            $data['name'],
            $data['code'] ?? null,
            $this->parentFrom($data['parent_id'] ?? null),
            $data['position'] ?? 0,
            $request->user(),
        );

        if ($request->boolean('setup')) {
            $academicYearId = $request->integer('academic_year_id');

            if ($academicYearId > 0) {
                $academicYear = AcademicYear::inSchool()->findOrFail($academicYearId);

                return to_route('academic-years.setup', [$academicYear, 'structure'])
                    ->with('success', 'Class created. Continue by building this year’s classes.');
            }

            return to_route('schools.setup', [current_school(), 'academic-year'])
                ->with('success', 'Class created. Continue by setting up an academic year.');
        }

        return redirect()
            ->route('academic-levels.show', $academicLevel)
            ->with('success', 'Academic level created. Add a cycle section to use it in a cycle.');
    }

    public function show(AcademicLevel $academicLevel): View
    {
        $academicLevel->load([
            'parent:id,name',
            'children:id,parent_id,name,position,status',
        ]);

        $cycleSections = $academicLevel->cycleSections()
            ->with(['academicYear:id,start_year,stop_year', 'homeroomTeacher:id,name'])
            ->orderByDesc('academic_year_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('pages.academic-level.show', compact('academicLevel', 'cycleSections'));
    }

    public function edit(AcademicLevel $academicLevel): View|RedirectResponse
    {
        if (!$academicLevel->isEditable()) {
            return redirect()
                ->route('academic-levels.show', $academicLevel)
                ->with('danger', 'An archived academic level cannot be edited.');
        }

        return view('pages.academic-level.edit', $this->formOptions($academicLevel) + compact('academicLevel'));
    }

    public function update(UpdateAcademicLevelRequest $request, AcademicLevel $academicLevel): RedirectResponse
    {
        $data = $request->validated();

        $this->updateAcademicLevel->update(
            $academicLevel,
            $data,
            $this->parentFrom($data['parent_id'] ?? null),
            $request->user(),
        );

        return redirect()
            ->route('academic-levels.show', $academicLevel)
            ->with('success', 'Academic level updated.');
    }

    public function changeStatus(ChangeAcademicLevelStatusRequest $request, AcademicLevel $academicLevel): RedirectResponse
    {
        $status = AcademicStructureStatus::from($request->validated('status'));

        $this->changeAcademicLevelStatus->change($academicLevel, $status, $request->user());

        return back()->with('success', "Academic level is now {$status->label()}.");
    }

    /**
     * Read the level list a form can choose from.
     *
     * @return array{academicLevels: Collection<int, AcademicLevel>}
     */
    private function formOptions(?AcademicLevel $except = null): array
    {
        $academicLevels = AcademicLevel::inSchool()
            ->where('status', AcademicStructureStatus::Active)
            ->when($except !== null, fn (Builder $query) => $query->whereKeyNot($except->id))
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        return compact('academicLevels');
    }

    private function parentFrom(int|string|null $parentId): ?AcademicLevel
    {
        return $parentId === null ? null : AcademicLevel::inSchool()->findOrFail($parentId);
    }

    private function readStatus(Request $request): ?AcademicStructureStatus
    {
        $status = $request->query('status');

        return is_string($status) ? AcademicStructureStatus::tryFrom($status) : null;
    }
}
