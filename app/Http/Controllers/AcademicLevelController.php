<?php

namespace App\Http\Controllers;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicLevelController extends Controller
{
    public function __construct()
    {
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
                ->where('is_group', true)
                ->find($request->integer('parent_id'))
            : null;

        return view('pages.academic-level.create', compact('preselectedParent'));
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

        return view('pages.academic-level.edit', compact('academicLevel'));
    }

    private function readStatus(Request $request): ?AcademicStructureStatus
    {
        $status = $request->query('status');

        return is_string($status) ? AcademicStructureStatus::tryFrom($status) : null;
    }
}
