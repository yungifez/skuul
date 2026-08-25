<?php

namespace App\Livewire;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Actions\Academic\RollForwardAcademicYearSetup;
use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\AcademicYear;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use LogicException;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ShowAcademicYear extends DataTableComponent
{
    use DispatchesStatusNotifications;
    use InteractsWithAprilTable;

    public AcademicYear $academicYear;

    public ?AcademicYear $previousAcademicYear = null;

    /** @var array{items: list<array{key: string, title: string, description: string, details: list<string>, count: int, will_create: bool}>, create_count: int}|null */
    public ?array $setupRolloverPreview = null;

    public bool $showSetupRolloverDialog = false;

    public function mount(?AcademicYear $academicYear = null): void
    {
        parent::mount();
        if ($academicYear === null) {
            throw new LogicException('An academic year is required.');
        }
        $this->academicYear = $academicYear->loadMissing('academicPeriods');
        $this->previousAcademicYear = AcademicYear::inSchool()
            ->where('start_year', '<', $academicYear->start_year)
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->first();
    }

    public function openSetupRolloverDialog(RollForwardAcademicYearSetup $rollForwardAcademicYearSetup): void
    {
        $this->authorize('update', $this->academicYear);

        if ($this->previousAcademicYear === null) {
            return;
        }

        try {
            $this->setupRolloverPreview = $rollForwardAcademicYearSetup->preview($this->previousAcademicYear, $this->academicYear);
            $this->showSetupRolloverDialog = true;
        } catch (InvalidValueException $exception) {
            $this->addError('rollover', $exception->getMessage());
        }
    }

    public function rollForwardSetup(RollForwardAcademicYearSetup $rollForwardAcademicYearSetup): void
    {
        $this->authorize('update', $this->academicYear);

        if ($this->previousAcademicYear === null) {
            return;
        }

        try {
            $created = $rollForwardAcademicYearSetup->rollForward(
                $this->previousAcademicYear,
                $this->academicYear,
                auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('rollover', $exception->getMessage());

            return;
        }

        $this->academicYear->refresh()->load('academicPeriods');
        $this->setupRolloverPreview = null;
        $this->showSetupRolloverDialog = false;
        $createdItems = [];

        if ($created['academic_periods'] > 0) {
            $createdItems[] = $created['academic_periods'].' reporting periods';
        }

        if ($created['instructional_model']) {
            $createdItems[] = 'the teaching approach';
        }

        $message = $createdItems === []
            ? "Everything from {$this->previousAcademicYear->name} is already set up."
            : 'Created '.implode(' and ', $createdItems)." from {$this->previousAcademicYear->name}.";

        $this->notify($message);
    }

    public function publishCalendar(PublishAcademicCalendar $publishAcademicCalendar): void
    {
        abort_unless(auth()->user()?->can('update', $this->academicYear), 403);

        try {
            $this->academicYear = $publishAcademicCalendar->publish($this->academicYear, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('calendar', $exception->getMessage());
        }
    }

    protected function builder(): Builder
    {
        return Exam::query()->whereRelation('academicPeriod', 'academic_year_id', $this->academicYear->id)->with('academicPeriod');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable(), Column::make('Academic period', 'academic_period_name')];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Exam $exam): array {
            $row = $exam->toArray();
            $row['academic_period_name'] = $exam->academicPeriod->name;
            $row['edit_url'] = route('exams.edit', $exam);
            $row['view_url'] = route('exams.show', $exam);
            $row['delete_url'] = route('exams.destroy', $exam);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $topLevelPeriods = $this->academicYear->academicPeriods
            ->whereNull('parent_id')
            ->values();

        return view('livewire.show-academic-year', array_merge($this->aprilTablePayload(), [
            'canEditExams' => auth()->user()->can('update exam'),
            'canDeleteExams' => auth()->user()->can('delete exam'),
            'canCreateExams' => auth()->user()->can('create exam') && $this->academicYear->isOpen(),
            'canEditCalendar' => auth()->user()->can('update', $this->academicYear),
            'isDraft' => $this->academicYear->status === AcademicPeriodStatus::Draft,
            'canRollForwardSetup' => $this->previousAcademicYear !== null
                && auth()->user()->can('update', $this->academicYear)
                && !$this->academicYear->status->isFrozen(),
            'lifecycleSteps' => [
                [
                    'status' => AcademicPeriodStatus::Draft,
                    'title' => 'Draft',
                    'description' => 'Build dates, periods and setup. Daily records are not ready yet.',
                ],
                [
                    'status' => AcademicPeriodStatus::Scheduled,
                    'title' => 'Scheduled',
                    'description' => 'The calendar is agreed and starts on a future date.',
                ],
                [
                    'status' => AcademicPeriodStatus::Open,
                    'title' => 'Open',
                    'description' => 'Staff can record the new work that belongs to this year.',
                ],
                [
                    'status' => AcademicPeriodStatus::Closing,
                    'title' => 'Closing',
                    'description' => 'Finish existing work and resolve the closing checks.',
                ],
                [
                    'status' => AcademicPeriodStatus::Closed,
                    'title' => 'Closed',
                    'description' => 'The year is protected as history. Reopen only with a reason.',
                ],
            ],
            'topLevelPeriods' => $topLevelPeriods,
        ]));
    }
}
