<?php

namespace App\Livewire;

use App\Actions\Curriculum\RollForwardAcademicCycleSections;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Copy one school year's sections into another as drafts.
 */
class RollForwardSections extends Component
{
    #[Url(as: 'source_academic_year_id')]
    public string $sourceAcademicYearId = '';

    #[Url(as: 'target_academic_year_id')]
    public string $targetAcademicYearId = '';

    /**
     * Whether the copy was opened from school setup, so it returns there.
     */
    #[Locked]
    public bool $setup = false;

    public function mount(bool $setup = false): void
    {
        Gate::authorize('create', AcademicCycleSection::class);

        $this->setup = $setup;

        $target = AcademicYear::inSchool()->find((int) $this->targetAcademicYearId ?: current_academic_year_id());
        $this->targetAcademicYearId = $target === null ? '' : (string) $target->id;

        if ($target !== null && $this->source() === null) {
            $this->sourceAcademicYearId = (string) (AcademicYear::inSchool()
                ->where('start_year', '<', $target->start_year)
                ->orderByDesc('start_year')
                ->orderByDesc('id')
                ->value('id') ?? '');
        }
    }

    public function rollForward(RollForwardAcademicCycleSections $rollForwardAcademicCycleSections): void
    {
        Gate::authorize('create', AcademicCycleSection::class);

        [$source, $target] = [$this->source(), $this->target()];

        if ($source === null || $target === null) {
            $this->addError('sourceAcademicYearId', 'Choose both school years.');

            return;
        }

        try {
            $created = $rollForwardAcademicCycleSections->rollForward($source, $target, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('targetAcademicYearId', $exception->getMessage());

            return;
        }

        $count = $created->count();
        session()->flash('success', $count === 0
            ? "{$target->name} already has every section of {$source->name}. Nothing was copied."
            : $count.' draft '.($count === 1 ? 'section was' : 'sections were')." created in {$target->name}. Learners, teachers, and timetables did not come along.");

        $this->setup
            ? $this->redirectRoute('schools.setup', [current_school(), 'classes'])
            : $this->redirectRoute('academic-cycle-sections.index', ['academic_year_id' => $target->id]);
    }

    public function render(RollForwardAcademicCycleSections $rollForwardAcademicCycleSections): View
    {
        [$source, $target] = [$this->source(), $this->target()];
        $preview = null;
        $problem = null;

        if ($source !== null && $target !== null) {
            try {
                $preview = $rollForwardAcademicCycleSections->preview($source, $target);
            } catch (InvalidValueException $exception) {
                $problem = $exception->getMessage();
            }
        }

        /** @var Collection<int, AcademicYear> $academicYears */
        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->orderByDesc('id')->get(['id', 'start_year', 'stop_year', 'status']);

        return view('livewire.roll-forward-sections', compact('academicYears', 'preview', 'problem', 'source', 'target'));
    }

    private function source(): ?AcademicYear
    {
        return $this->sourceAcademicYearId === '' ? null : AcademicYear::inSchool()->find((int) $this->sourceAcademicYearId);
    }

    private function target(): ?AcademicYear
    {
        return $this->targetAcademicYearId === '' ? null : AcademicYear::inSchool()->find((int) $this->targetAcademicYearId);
    }
}
