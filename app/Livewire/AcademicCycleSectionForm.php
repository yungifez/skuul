<?php

namespace App\Livewire;

use App\Actions\Curriculum\CreateAcademicCycleSection;
use App\Actions\Curriculum\UpdateAcademicCycleSection;
use App\Enums\AcademicStructureStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Add a section to one school year, or change one.
 */
class AcademicCycleSectionForm extends Component
{
    /** @var array<int, string> */
    private const DETAILS = ['label', 'stream', 'shift', 'language', 'room'];

    #[Locked]
    public ?AcademicCycleSection $academicCycleSection = null;

    /**
     * Where a save opened from a setup screen returns to.
     */
    #[Locked]
    public bool $setup = false;

    #[Locked]
    public bool $schoolSetup = false;

    public string $academicYearId = '';

    public string $academicLevelId = '';

    public string $name = '';

    public string $homeroomTeacherId = '';

    public string $label = '';

    public string $stream = '';

    public string $shift = '';

    public string $language = '';

    public string $room = '';

    public string $capacity = '';

    public string $position = '0';

    public function mount(
        ?AcademicCycleSection $academicCycleSection = null,
        bool $setup = false,
        bool $schoolSetup = false,
        ?int $preselectedAcademicYearId = null,
        ?int $preselectedAcademicLevelId = null,
    ): void {
        if ($academicCycleSection?->exists !== true) {
            Gate::authorize('create', AcademicCycleSection::class);

            $this->setup = $setup;
            $this->schoolSetup = $setup && $schoolSetup;

            if ($preselectedAcademicYearId !== null && $this->academicYears()->contains('id', $preselectedAcademicYearId)) {
                $this->academicYearId = (string) $preselectedAcademicYearId;
            }

            if ($preselectedAcademicLevelId !== null && $this->academicLevels()->contains('id', $preselectedAcademicLevelId)) {
                $this->academicLevelId = (string) $preselectedAcademicLevelId;
            }

            return;
        }

        Gate::authorize('update', $academicCycleSection);

        $this->academicCycleSection = $academicCycleSection;
        $this->name = $academicCycleSection->name;
        $this->homeroomTeacherId = $academicCycleSection->homeroom_teacher_id === null ? '' : (string) $academicCycleSection->homeroom_teacher_id;

        foreach (self::DETAILS as $detail) {
            $this->{$detail} = (string) $academicCycleSection->{$detail};
        }

        $this->capacity = $academicCycleSection->capacity === null ? '' : (string) $academicCycleSection->capacity;
        $this->position = (string) $academicCycleSection->position;
    }

    public function save(CreateAcademicCycleSection $createAcademicCycleSection, UpdateAcademicCycleSection $updateAcademicCycleSection): void
    {
        $section = $this->academicCycleSection;

        if ($section === null) {
            Gate::authorize('create', AcademicCycleSection::class);
        } else {
            Gate::authorize('update', $section);
        }

        $this->name = trim($this->name);

        foreach (self::DETAILS as $detail) {
            $this->{$detail} = trim($this->{$detail});
        }

        $yearId = $section->academic_year_id ?? (int) $this->academicYearId;
        $levelId = $section->academic_level_id ?? (int) $this->academicLevelId;

        $this->validate([
            'academicYearId' => $section === null ? ['required', Rule::in($this->idsOf($this->academicYears()))] : [],
            'academicLevelId' => $section === null ? ['required', Rule::in($this->idsOf($this->academicLevels()))] : [],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('academic_cycle_sections', 'name')
                    ->where('school_id', current_school_id())
                    ->where('academic_year_id', $yearId)
                    ->where('academic_level_id', $levelId)
                    ->ignore($section?->id),
            ],
            'homeroomTeacherId' => ['nullable', Rule::in($this->idsOf($this->teachers()))],
            'label' => ['nullable', 'string', 'max:255'],
            'stream' => ['nullable', 'string', 'max:100'],
            'shift' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:100'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.unique' => 'This class already has a section with this name in this school year. Choose another name.',
            'homeroomTeacherId.in' => 'Choose one of this school’s current teachers, or none.',
        ], [
            'academicYearId' => strtolower(school_term('academic_year', 'school year')),
            'academicLevelId' => strtolower(school_term('class_level', 'class')),
            'homeroomTeacherId' => strtolower(school_term('homeroom_teacher', 'class teacher')),
            'position' => 'display order',
        ]);

        $details = ['name' => $this->name, 'capacity' => $this->capacity === '' ? null : (int) $this->capacity, 'position' => (int) $this->position];

        foreach (self::DETAILS as $detail) {
            $details[$detail] = $this->{$detail} === '' ? null : $this->{$detail};
        }

        $teacher = $this->homeroomTeacherId === '' ? null : $this->teachers()->firstWhere('id', (int) $this->homeroomTeacherId);

        try {
            if ($section === null) {
                $academicYear = AcademicYear::inSchool()->findOrFail($yearId);
                $saved = $createAcademicCycleSection->create($academicYear, AcademicLevel::inSchool()->findOrFail($levelId), $this->name, $details, $teacher, auth()->user());
            } else {
                $saved = $updateAcademicCycleSection->update($section, $details, $teacher, auth()->user());
            }
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            return;
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        if ($section !== null) {
            session()->flash('success', school_term('section', 'Section').' updated.');
            $this->redirectRoute('academic-cycle-sections.show', $saved);

            return;
        }

        if ($this->schoolSetup) {
            session()->flash('success', 'Section created for the year. Review it in the class setup.');
            $this->redirectRoute('schools.setup', [current_school(), 'classes']);

            return;
        }

        if ($this->setup) {
            session()->flash('success', 'Section created for the year. Review it in the setup tree.');
            $this->redirectRoute('academic-years.setup', [$yearId, 'structure']);

            return;
        }

        session()->flash('success', school_term('section', 'Section').' created as a draft. Activate it when the setup is right.');
        $this->redirectRoute('academic-cycle-sections.show', $saved);
    }

    public function render(): View
    {
        return view('livewire.academic-cycle-section-form', [
            'academicYears' => $this->academicCycleSection === null ? $this->academicYears() : collect(),
            'academicLevels' => $this->academicCycleSection === null ? $this->academicLevels() : collect(),
            'teachers' => $teachers = $this->teachers(),
            'departedTeacher' => $this->homeroomTeacherId !== '' && !$teachers->contains('id', (int) $this->homeroomTeacherId)
                ? $this->academicCycleSection?->homeroomTeacher()->first(['users.id', 'users.name'])
                : null,
        ]);
    }

    /**
     * @return Collection<int, AcademicYear>
     */
    private function academicYears(): Collection
    {
        return AcademicYear::inSchool()->orderByDesc('start_year')->get(['id', 'start_year', 'stop_year', 'status']);
    }

    /**
     * @return Collection<int, AcademicLevel>
     */
    private function academicLevels(): Collection
    {
        return AcademicLevel::inSchool()
            ->where('status', AcademicStructureStatus::Active)
            ->where('is_group', false)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * The teachers of this school who may lead a section.
     *
     * @return Collection<int, User>
     */
    private function teachers(): Collection
    {
        return User::ofSchool()->role(Role::Teacher->value)->orderBy('name')->get(['users.id', 'users.name']);
    }

    /**
     * @param  Collection<int, covariant \Illuminate\Database\Eloquent\Model>  $records
     * @return array<int, string>
     */
    private function idsOf(Collection $records): array
    {
        return $records->modelKeys() === [] ? [] : array_map('strval', $records->modelKeys());
    }
}
