<?php

namespace App\Livewire;

use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Move learners from one section of the current year to another.
 */
class PromoteStudents extends Component
{
    use DispatchesStatusNotifications;

    /** @var array<int, array{id: int, label: string}> */
    #[Locked]
    public array $cycleSections = [];

    public ?int $sourceAcademicCycleSectionId = null;

    public ?int $destinationAcademicCycleSectionId = null;

    /** @var array<int, array{id: int, name: string, admission_number: string|null}> */
    #[Locked]
    public array $students = [];

    /** @var array<int, int|string> */
    public array $selectedStudentIds = [];

    public function mount(): void
    {
        Gate::authorize('promote', Promotion::class);

        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')
            ->where('academic_year_id', current_academic_year_id())
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id' => $cycleSection->id,
                'label' => $cycleSection->academicLevel->name.' · '.($cycleSection->label ?? $cycleSection->name),
            ])
            ->all();
    }

    /**
     * Drop the reviewed list when either section changes, so the confirm step never moves learners the page no longer names.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['sourceAcademicCycleSectionId', 'destinationAcademicCycleSectionId'], true)) {
            $this->students = [];
            $this->selectedStudentIds = [];
        }
    }

    public function loadStudents(): void
    {
        Gate::authorize('promote', Promotion::class);

        $this->validate($this->sectionRules());

        $this->students = User::activeStudents()
            ->whereHas('studentRecord', fn ($query) => $query->where('academic_cycle_section_id', $this->sourceAcademicCycleSectionId))
            ->with('studentRecord')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $student): array => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_number' => $student->studentRecord?->admission_number,
            ])
            ->all();
        $this->selectedStudentIds = array_column($this->students, 'id');

        if ($this->students === []) {
            $this->addError('sourceAcademicCycleSectionId', 'No active learners are in this section.');
        }
    }

    public function promote(StudentService $studentService): void
    {
        Gate::authorize('promote', Promotion::class);

        $this->validate($this->sectionRules() + [
            'selectedStudentIds' => ['required', 'array', 'min:1'],
            'selectedStudentIds.*' => ['integer', Rule::in(array_column($this->students, 'id'))],
        ], [
            'selectedStudentIds.required' => 'Choose at least one learner to move.',
            'selectedStudentIds.min' => 'Choose at least one learner to move.',
        ]);

        try {
            $promotion = $studentService->promoteStudents([
                'source_academic_cycle_section_id' => (int) $this->sourceAcademicCycleSectionId,
                'destination_academic_cycle_section_id' => (int) $this->destinationAcademicCycleSectionId,
                'student_id' => array_map('intval', $this->selectedStudentIds),
            ]);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');
            $this->students = [];
            $this->selectedStudentIds = [];

            return;
        }

        $count = count($promotion->students);
        session()->flash('success', $count === 1 ? 'One learner was moved.' : "{$count} learners were moved.");

        if (Gate::allows('view', $promotion)) {
            $this->redirectRoute('students.promotions.show', $promotion);

            return;
        }

        $this->redirectRoute('students.promote');
    }

    /**
     * Only sections of the current year that this page listed can be chosen.
     *
     * @return array<string, array<int, mixed>>
     */
    private function sectionRules(): array
    {
        $listedIds = array_column($this->cycleSections, 'id');

        return [
            'sourceAcademicCycleSectionId' => ['required', 'integer', Rule::in($listedIds)],
            'destinationAcademicCycleSectionId' => ['required', 'integer', Rule::in($listedIds), 'different:sourceAcademicCycleSectionId'],
        ];
    }

    public function render(): View
    {
        return view('livewire.promote-students');
    }
}
