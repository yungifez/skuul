<?php

namespace App\Http\Controllers;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicCycleSectionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademicCycleSection::class, 'academicCycleSection');
    }

    public function index(): View
    {
        return view('pages.academic-cycle-section.index');
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

    public function show(AcademicCycleSection $academicCycleSection): View
    {
        $academicCycleSection->load([
            'academicYear:id,start_year,stop_year,status',
            'academicLevel:id,name,code',
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
        $academicCycleSection->load(['academicYear:id,start_year,stop_year,status', 'academicLevel:id,name']);

        if (!$academicCycleSection->isEditable()) {
            return redirect()
                ->route('academic-cycle-sections.show', $academicCycleSection)
                ->with('danger', 'This '.strtolower(school_term('section', 'section')).' is archived or its '.strtolower(school_term('academic_year', 'school year')).' is closed, so its setup cannot change.');
        }

        return view('pages.academic-cycle-section.edit', compact('academicCycleSection'));
    }

    /**
     * Show what a roll-forward would copy before anything is written.
     */
    public function rollForwardForm(): View
    {
        $this->authorize('create', AcademicCycleSection::class);

        return view('pages.academic-cycle-section.roll-forward');
    }

    /**
     * Read the years and classes, so the create page can say what is missing first.
     *
     * @return array{academicYears: Collection<int, AcademicYear>, academicLevels: Collection<int, AcademicLevel>}
     */
    private function formOptions(): array
    {
        $academicYears = $this->academicYears();
        $academicLevels = AcademicLevel::inSchool()
            ->where('status', AcademicStructureStatus::Active)
            ->where('is_group', false)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        return compact('academicYears', 'academicLevels');
    }

    /**
     * @return Collection<int, AcademicYear>
     */
    private function academicYears(): Collection
    {
        return AcademicYear::inSchool()->orderByDesc('start_year')->get(['id', 'start_year', 'stop_year', 'status']);
    }

    /**
     * @param  array<int, int>  $allowed
     */
    private function selectedId(Request $request, string $key, array $allowed): ?int
    {
        $value = $request->query($key);

        if (!is_string($value) || $value === '' || !ctype_digit($value)) {
            return null;
        }

        return in_array((int) $value, $allowed, true) ? (int) $value : null;
    }
}
