<?php

namespace App\Livewire;

use App\Actions\Exam\SaveExam;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\Exam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Rename an exam, give it new dates, or move it to another period of its year.
 */
class EditExamForm extends Component
{
    #[Locked]
    public Exam $exam;

    public string $name = '';

    public string $description = '';

    public string $startDate = '';

    public string $stopDate = '';

    public string $academicPeriodId = '';

    public function mount(Exam $exam): void
    {
        Gate::authorize('update', $exam);

        $this->exam = $exam;
        $this->name = $exam->name;
        $this->description = (string) $exam->description;
        $this->startDate = (string) $exam->start_date->format('Y-m-d');
        $this->stopDate = (string) $exam->stop_date->format('Y-m-d');
        $this->academicPeriodId = (string) $exam->academic_period_id;
    }

    public function save(SaveExam $saveExam): void
    {
        Gate::authorize('update', $this->exam);

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

        Gate::authorize('updateForAcademicPeriod', [Exam::class, $period]);

        try {
            $this->exam = $saveExam->update($this->exam, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'academic_period_id' => $period->id,
                'start_date' => $this->startDate,
                'stop_date' => $this->stopDate,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $field = match (true) {
                str_contains($exception->getMessage(), 'already has an exam') => 'name',
                str_contains($exception->getMessage(), 'gradebook') => 'academicPeriodId',
                default => 'startDate',
            };
            $this->addError($field, $exception->getMessage());

            return;
        }

        session()->flash('success', 'The exam was saved.');
        $this->redirectRoute('academic-years.show', $period->academic_year_id);
    }

    public function render(): View
    {
        return view('livewire.edit-exam-form', ['academicPeriods' => $this->periods()]);
    }

    /**
     * Get the periods of the exam's year that still take exams, and its own period.
     *
     * @return Collection<int, AcademicPeriod>
     */
    private function periods(): Collection
    {
        $academicYearId = AcademicPeriod::query()->inSchool()->whereKey($this->exam->academic_period_id)->value('academic_year_id');

        return AcademicPeriod::query()
            ->inSchool()
            ->where('academic_year_id', $academicYearId)
            ->with('academicYear')
            ->get()
            ->filter(fn (AcademicPeriod $period): bool => $period->id === $this->exam->academic_period_id || $period->status->acceptsExamPlanning())
            ->values();
    }
}
