<?php

namespace App\Livewire;

use App\Actions\Curriculum\UpdateCourseOfferingRoster;
use App\Enums\AcademicStructureStatus;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Choose who attends a course offering.
 */
class EditCourseOfferingRoster extends Component
{
    #[Locked]
    public CourseOffering $courseOffering;

    #[Locked]
    public bool $setup = false;

    public string $rosterMode = '';

    /** @var array<int, int|string> */
    public array $academicCycleSectionIds = [];

    /** @var array<int, int|string> */
    public array $studentRecordIds = [];

    public function mount(bool $setup = false): void
    {
        Gate::authorize('update', $this->courseOffering);

        $this->setup = $setup;
        $this->courseOffering->loadMissing(['academicLevel', 'academicPeriod', 'academicYear', 'subject', 'cycleSections', 'studentRecords']);
        $this->rosterMode = $this->courseOffering->roster_mode->value;
        $this->academicCycleSectionIds = $this->stillChoosable($this->courseOffering->cycleSections->modelKeys(), $this->sections()->modelKeys());
        $this->studentRecordIds = $this->stillChoosable($this->courseOffering->studentRecords->modelKeys(), $this->learners()->modelKeys());
    }

    /**
     * Keep only the choices the form still offers.
     *
     * A learner who left or moved class has no box to untick, so keeping
     * them chosen would make every save fail.
     *
     * @param  array<int, int>  $chosen
     * @param  array<int, int>  $offered
     * @return array<int, int>
     */
    private function stillChoosable(array $chosen, array $offered): array
    {
        return array_values(array_intersect($chosen, $offered));
    }

    public function save(UpdateCourseOfferingRoster $updateCourseOfferingRoster): void
    {
        Gate::authorize('update', $this->courseOffering);

        $this->validate([
            'rosterMode' => ['required', Rule::in(array_map(fn (RosterMode $mode): string => $mode->value, $this->rosterModes()))],
            'academicCycleSectionIds' => ['array'],
            'academicCycleSectionIds.*' => ['integer', 'distinct', Rule::in($this->sections()->modelKeys())],
            'studentRecordIds' => ['array'],
            'studentRecordIds.*' => ['integer', 'distinct', Rule::in($this->learners()->modelKeys())],
        ], [
            'academicCycleSectionIds.*.in' => 'Choose sections of this class and year.',
            'studentRecordIds.*.in' => 'Choose learners who attend this class this year.',
        ]);

        $rosterMode = RosterMode::from($this->rosterMode);

        try {
            $updateCourseOfferingRoster->update(
                $this->courseOffering,
                $rosterMode,
                $rosterMode->usesHomeSections() ? array_map('intval', $this->academicCycleSectionIds) : [],
                $rosterMode === RosterMode::IndividualRoster ? array_map('intval', $this->studentRecordIds) : [],
                null,
                auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('rosterMode', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Roster updated.');

        $this->setup
            ? $this->redirectRoute('academic-years.setup', [$this->courseOffering->academic_year_id, 'subjects'])
            : $this->redirectRoute('course-offerings.index');
    }

    /**
     * The ways of choosing learners this offering may use.
     *
     * @return array<int, RosterMode>
     */
    private function rosterModes(): array
    {
        if ($this->courseOffering->academicLevel->is_group) {
            return [RosterMode::AcademicLevel];
        }

        $rosterModes = instructional_model($this->courseOffering->academicYear)->rosterModes();

        if (!in_array($this->courseOffering->roster_mode, $rosterModes, true)) {
            $rosterModes[] = $this->courseOffering->roster_mode;
        }

        return $rosterModes;
    }

    /**
     * @return Collection<int, AcademicCycleSection>
     */
    private function sections(): Collection
    {
        return AcademicCycleSection::inSchool()
            ->where('academic_year_id', $this->courseOffering->academic_year_id)
            ->where('academic_level_id', $this->courseOffering->academic_level_id)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, StudentRecord>
     */
    private function learners(): Collection
    {
        return StudentRecord::inSchool()
            ->attending()
            ->with(['academicCycleSection', 'user:id,name'])
            ->whereHas('academicCycleSection', function (Builder $query): void {
                $query->where('academic_year_id', $this->courseOffering->academic_year_id)
                    ->where('academic_level_id', $this->courseOffering->academic_level_id);
            })
            ->orderBy('admission_number')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.edit-course-offering-roster', [
            'rosterModes' => $this->rosterModes(),
            'sections' => $this->sections(),
            'learners' => $this->rosterMode === RosterMode::IndividualRoster->value ? $this->learners() : new Collection,
        ]);
    }
}
