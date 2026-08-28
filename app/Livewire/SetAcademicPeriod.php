<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class SetAcademicPeriod extends Component
{
    public bool $compact = false;

    /** @var Collection<int, AcademicPeriod> */
    public Collection $academicPeriods;

    public ?AcademicYear $academicYear = null;

    public ?AcademicPeriod $currentPeriod = null;

    public ?AcademicPeriod $workingPeriod = null;

    public ?string $calendarError = null;

    public function mount(bool $compact = false): void
    {
        $this->compact = $compact;
        $this->academicYear = current_academic_year();

        if ($this->academicYear === null) {
            $this->academicPeriods = new Collection;

            return;
        }

        $this->academicPeriods = $this->academicYear->topLevelPeriods()->get();
        $coveringPeriods = $this->academicYear->periodsForDate();
        $this->currentPeriod = $coveringPeriods->count() === 1 ? $coveringPeriods->first() : null;
        $this->workingPeriod = current_academic_period();
        $this->calendarError = academic_period_context()->resolutionError();
    }

    public function canChange(): bool
    {
        return auth()->user()?->can('set academic period') ?? false;
    }

    public function render(): View
    {
        return view('livewire.set-academic-period');
    }
}
