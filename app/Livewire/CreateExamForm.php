<?php

namespace App\Livewire;

use App\Actions\Exam\SaveExam;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Exam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Plan an exam in one reporting period of a school year.
 */
class CreateExamForm extends Component
{
    #[Locked]
    public ?int $academicYearId = null;

    public string $name = '';

    public string $description = '';

    public string $startDate = '';

    public string $stopDate = '';

    public string $academicPeriodId = '';

    public function mount(): void
    {
        Gate::authorize('create', Exam::class);

        $this->academicYearId = AcademicYear::inSchool()
            ->find(request()->integer('academic_year_id') ?: current_academic_year_id())?->id;

        $periods = $this->periods();
        $selectedPeriod = $periods->firstWhere('id', request()->integer('academic_period_id'))
            ?? $periods->firstWhere('id', current_academic_period_id())
            ?? $periods->first();

        $this->academicPeriodId = (string) $selectedPeriod?->id;
    }

    public function save(SaveExam $saveExam): void
    {
        Gate::authorize('create', Exam::class);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'academicPeriodId' => ['required', 'integer'],
            'startDate' => ['required', 'date'],
            'stopDate' => ['required', 'date', 'after_or_equal:startDate'],
        ], [], [
            'academicPeriodId' => 'reporting period',
            'startDate' => 'start date',
            'stopDate' => 'end date',
        ]);

        $period = $this->periods()->firstWhere('id', (int) $this->academicPeriodId);

        if ($period === null) {
            $this->addError('academicPeriodId', 'Choose a reporting period of this school year that is still being planned.');

            return;
        }

        Gate::authorize('createForAcademicPeriod', [Exam::class, $period]);

        try {
            $saveExam->create([
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'academic_period_id' => $period->id,
                'start_date' => $this->startDate,
                'stop_date' => $this->stopDate,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError(str_contains($exception->getMessage(), 'already has an exam') ? 'name' : 'startDate', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The exam was planned.');
        $this->redirectRoute('academic-years.show', $period->academic_year_id);
    }

    public function render(): View
    {
        return view('livewire.create-exam-form', [
            'academicYear' => $this->academicYearId === null ? null : AcademicYear::inSchool()->find($this->academicYearId),
            'academicPeriods' => $this->periods(),
        ]);
    }

    /**
     * Get the periods of the chosen year that still take new exams.
     *
     * @return Collection<int, AcademicPeriod>
     */
    private function periods(): Collection
    {
        if ($this->academicYearId === null) {
            return collect();
        }

        return AcademicPeriod::query()
            ->inSchool()
            ->where('academic_year_id', $this->academicYearId)
            ->with('academicYear')
            ->get()
            ->filter(fn (AcademicPeriod $period): bool => $period->status->acceptsExamPlanning())
            ->values();
    }
}
