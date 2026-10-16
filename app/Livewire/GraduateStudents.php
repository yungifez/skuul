<?php

namespace App\Livewire;

use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\Graduation;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Graduate the learners of one section of the current year.
 */
class GraduateStudents extends Component
{
    use DispatchesStatusNotifications;

    /** @var array<int, array{id: int, label: string}> */
    #[Locked]
    public array $cycleSections = [];

    public ?int $academicCycleSectionId = null;

    /** @var array<int, array{id: int, name: string, admission_number: string|null}> */
    #[Locked]
    public array $students = [];

    /** @var array<int, int|string> */
    public array $selectedStudentIds = [];

    public string $reason = '';

    public function mount(): void
    {
        Gate::authorize('graduate', Graduation::class);

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
     * Drop the reviewed list when the section changes, so the confirm step never graduates learners the page no longer names.
     */
    public function updatedAcademicCycleSectionId(): void
    {
        $this->students = [];
        $this->selectedStudentIds = [];
    }

    public function loadStudents(): void
    {
        Gate::authorize('graduate', Graduation::class);

        $this->validate($this->sectionRules());

        $this->students = User::activeStudents()
            ->whereHas('studentRecord', fn ($query) => $query->where('academic_cycle_section_id', $this->academicCycleSectionId))
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
            $this->addError('academicCycleSectionId', 'No active learners are in this section.');
        }
    }

    public function graduate(StudentService $studentService): void
    {
        Gate::authorize('graduate', Graduation::class);

        $this->validate($this->sectionRules() + [
            'selectedStudentIds' => ['required', 'array', 'min:1'],
            'selectedStudentIds.*' => ['integer', Rule::in(array_column($this->students, 'id'))],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'selectedStudentIds.required' => 'Choose at least one learner to graduate.',
            'selectedStudentIds.min' => 'Choose at least one learner to graduate.',
        ]);

        try {
            $count = $studentService->graduateStudents([
                'academic_cycle_section_id' => (int) $this->academicCycleSectionId,
                'student_id' => array_map('intval', $this->selectedStudentIds),
                'reason' => trim($this->reason) === '' ? null : trim($this->reason),
            ]);
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');
            $this->students = [];
            $this->selectedStudentIds = [];

            return;
        }

        session()->flash('success', $count === 1 ? 'One learner graduated.' : "{$count} learners graduated.");
        $this->redirectRoute(Gate::allows('viewAny', Graduation::class) ? 'students.graduations' : 'students.graduate');
    }

    /**
     * Only sections of the current year that this page listed can be chosen.
     *
     * @return array<string, array<int, mixed>>
     */
    private function sectionRules(): array
    {
        return [
            'academicCycleSectionId' => ['required', 'integer', Rule::in(array_column($this->cycleSections, 'id'))],
        ];
    }

    public function render(): View
    {
        return view('livewire.graduate-students');
    }
}
