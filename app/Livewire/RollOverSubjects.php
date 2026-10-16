<?php

namespace App\Livewire;

use App\Actions\Curriculum\RollForwardCourseOfferings;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Copy last year's subject offerings into a new year as drafts.
 */
class RollOverSubjects extends Component
{
    #[Url(as: 'source_academic_year_id')]
    public string $sourceAcademicYearId = '';

    #[Url(as: 'target_academic_year_id')]
    public string $targetAcademicYearId = '';

    #[Locked]
    public bool $setup = false;

    public function mount(bool $setup = false): void
    {
        Gate::authorize('create', CourseOffering::class);

        $this->setup = $setup;

        $target = AcademicYear::inSchool()->find((int) $this->targetAcademicYearId ?: current_academic_year_id());
        $this->targetAcademicYearId = $target === null ? '' : (string) $target->id;

        if ($target !== null && AcademicYear::inSchool()->find((int) $this->sourceAcademicYearId) === null) {
            $this->sourceAcademicYearId = (string) (AcademicYear::inSchool()
                ->where('start_year', '<', $target->start_year)
                ->orderByDesc('start_year')
                ->orderByDesc('id')
                ->value('id') ?? '');
        }
    }

    public function rollOver(RollForwardCourseOfferings $rollForwardCourseOfferings): void
    {
        Gate::authorize('create', CourseOffering::class);

        [$source, $target] = [$this->source(), $this->target()];

        if ($source === null || $target === null) {
            $this->addError('sourceAcademicYearId', 'Choose both school years.');

            return;
        }

        try {
            $created = $rollForwardCourseOfferings->rollForward($source, $target, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('targetAcademicYearId', $exception->getMessage());

            return;
        }

        $count = $created->count();
        session()->flash('success', $count === 0
            ? "Nothing new to copy into {$target->name}."
            : $count.' '.($count === 1 ? 'subject was' : 'subjects were')." copied into {$target->name} as drafts.");

        $this->setup
            ? $this->redirectRoute('academic-years.setup', [$target, 'subjects'])
            : $this->redirectRoute('course-offerings.index');
    }

    private function source(): ?AcademicYear
    {
        return $this->sourceAcademicYearId === '' ? null : AcademicYear::inSchool()->find((int) $this->sourceAcademicYearId);
    }

    private function target(): ?AcademicYear
    {
        return $this->targetAcademicYearId === '' ? null : AcademicYear::inSchool()->find((int) $this->targetAcademicYearId);
    }

    public function render(RollForwardCourseOfferings $rollForwardCourseOfferings): View
    {
        [$source, $target] = [$this->source(), $this->target()];
        $preview = null;
        $problem = null;

        if ($source !== null && $target !== null) {
            try {
                $preview = $rollForwardCourseOfferings->preview($source, $target);
            } catch (InvalidValueException $exception) {
                $problem = $exception->getMessage();
            }
        }

        /** @var Collection<int, AcademicYear> $academicYears */
        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->orderByDesc('id')->get();

        return view('livewire.roll-over-subjects', compact('academicYears', 'preview', 'problem', 'source', 'target'));
    }
}
