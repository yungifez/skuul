<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\AcademicCycleSection;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Admit a learner into an active section of the current year.
 */
class CreateStudentForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    /** @var array<int, array{id: int, label: string}> */
    #[Locked]
    public array $cycleSections = [];

    public string $academicCycleSectionId = '';

    public string $admissionNumber = '';

    public string $admissionDate = '';

    public function mount(): void
    {
        Gate::authorize('create', [User::class, 'student']);

        $this->admissionDate = school_today()->toDateString();
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')
            ->where('academic_year_id', current_academic_year_id())
            ->where('status', AcademicStructureStatus::Active)
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id' => $cycleSection->id,
                'label' => $cycleSection->academicLevel->name.' · '.($cycleSection->label ?? $cycleSection->name),
            ])
            ->all();
    }

    public function save(StudentService $studentService): void
    {
        Gate::authorize('create', [User::class, 'student']);

        $this->trimPersonDetails();
        $this->admissionNumber = trim($this->admissionNumber);
        $this->validate($this->personDetailRules() + [
            'academicCycleSectionId' => ['required', 'integer', Rule::in(array_column($this->cycleSections, 'id'))],
            'admissionNumber' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('student_records', 'admission_number')->where('school_id', current_school_id()),
            ],
            'admissionDate' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.school_today()->toDateString()],
        ], [
            'academicCycleSectionId.required' => $this->cycleSections === []
                ? 'No '.strtolower(school_term('section', 'section')).' is active this year, so there is nowhere to admit the learner yet.'
                : 'Choose the section the learner joins.',
            'academicCycleSectionId.in' => 'Choose an active section of the current year.',
        ], $this->personDetailAttributes() + [
            'admissionNumber' => 'admission number',
            'admissionDate' => 'date of admission',
        ]);

        try {
            $student = $studentService->createStudent($this->personDetails() + [
                'academic_cycle_section_id' => (int) $this->academicCycleSectionId,
                'admission_number' => $this->admissionNumber === '' ? null : $this->admissionNumber,
                'admission_date' => $this->admissionDate,
            ]);
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        } catch (ApplicationException $exception) {
            $this->addError('academicCycleSectionId', $exception->getMessage());

            return;
        }

        session()->flash('success', $student->isAwaitingInvitationAcceptance()
            ? "{$student->name} was admitted. We emailed them a link to set a password."
            : "{$student->name} was admitted and can sign in with their existing password.");

        if (Gate::allows('view', [$student, 'student'])) {
            $this->redirectRoute('students.show', $student);

            return;
        }

        $this->redirectRoute('students.create');
    }

    public function render(): View
    {
        return view('livewire.create-student-form');
    }
}
