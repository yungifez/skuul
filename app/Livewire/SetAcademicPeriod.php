<?php

namespace App\Livewire;

use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Services\AcademicPeriod\AcademicPeriodService;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
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

    public ?int $workingPeriodId = null;

    /** @var Collection<int, AcademicYear> */
    public Collection $academicYears;

    public ?int $workingYearId = null;

    public function mount(bool $compact = false): void
    {
        $this->compact = $compact;
        $this->academicYear = current_academic_year();
        $this->workingYearId = $this->academicYear?->id;
        $this->academicYears = $this->canChangeYear()
            ? AcademicYear::inSchool()->where('status', '!=', AcademicPeriodStatus::Draft)->orderByDesc('starts_on')->get()
            : new Collection;

        if ($this->academicYear === null) {
            $this->academicPeriods = new Collection;

            return;
        }

        $this->academicPeriods = $this->academicYear->topLevelPeriods()->get();
        $coveringPeriods = $this->academicPeriods->filter(fn (AcademicPeriod $period): bool => $period->covers());
        $this->currentPeriod = $coveringPeriods->count() === 1 ? $coveringPeriods->first() : null;
        $this->workingPeriod = current_academic_period();
        $this->workingPeriodId = $this->workingPeriod?->id;
        $this->calendarError = academic_period_context()->resolutionError();
    }

    public function canChange(): bool
    {
        return auth()->user()?->can('set academic period') ?? false;
    }

    public function canChangeYear(): bool
    {
        return auth()->user()?->can('set academic year') ?? false;
    }

    /**
     * Switch the working school year for the signed-in person as soon as they pick one.
     */
    public function updatedWorkingYearId(): void
    {
        $this->setWorkingYear(app(AcademicYearService::class));
    }

    public function setWorkingYear(AcademicYearService $academicYears): void
    {
        Gate::authorize('setAcademicYear', AcademicYear::class);

        $this->validate(['workingYearId' => ['required', 'integer']]);

        try {
            $academicYears->setAcademicYear($this->workingYearId, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('workingYearId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Working '.strtolower(school_term('academic_year', 'school year')).' saved for you.');

        $this->redirect(request()->header('Referer') ?? route('dashboard'));
    }

    /**
     * Save the working period for the signed-in person as soon as they pick one.
     */
    public function updatedWorkingPeriodId(): void
    {
        $this->setWorkingPeriod(app(AcademicPeriodService::class));
    }

    public function setWorkingPeriod(AcademicPeriodService $academicPeriods): void
    {
        Gate::authorize('setAcademicPeriod', AcademicPeriod::class);

        $this->validate(['workingPeriodId' => ['required', 'integer']]);
        $academicPeriod = AcademicPeriod::inSchool()->findOrFail($this->workingPeriodId);

        try {
            $academicPeriods->setAcademicPeriod($academicPeriod, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('workingPeriodId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Working '.strtolower(school_term('period', 'term')).' saved for you.');

        $this->redirect(request()->header('Referer') ?? route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.set-academic-period');
    }
}
