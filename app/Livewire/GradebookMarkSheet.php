<?php

namespace App\Livewire;

use App\Actions\Gradebook\ApproveResult;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Actions\Gradebook\RejectResult;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Enums\ResultApprovalStatus;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\CourseOffering;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Enter the marks for one assessment, then send each learner's result on.
 *
 * The sheet remembers every mark as it read it. When somebody else changed a
 * mark in the meantime, saving over it needs a second press, so two teachers
 * of one class never overwrite each other without knowing.
 */
class GradebookMarkSheet extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public CourseOffering $courseOffering;

    #[Url(as: 'assessment', except: '')]
    public string $gradeItemId = '';

    /** @var array<int, array{state: string, points: string, option: string, comment: string}> */
    public array $marks = [];

    /**
     * The marks as they were read, by learner.
     *
     * @var array<int, array{state: string, points: string, option: string, comment: string}>
     */
    #[Locked]
    public array $original = [];

    /** @var list<int> */
    #[Locked]
    public array $overwrites = [];

    #[Locked]
    public string $openItemId = '';

    public ?int $rejectingResultId = null;

    public string $rejectReason = '';

    public function mount(CourseOffering $courseOffering): void
    {
        Gate::authorize('viewGradebook', $courseOffering);

        $this->courseOffering = $courseOffering;

        if ($this->selectedItem() === null) {
            $this->gradeItemId = (string) ($this->items()->first()->id ?? '');
        }

        $this->readMarks();
    }

    /**
     * Open another assessment, unless that would lose typed marks.
     */
    public function updatedGradeItemId(): void
    {
        if ($this->changedStudentIds() !== []) {
            $this->gradeItemId = $this->openItemId;
            $this->addError('gradeItemId', 'Save these marks first, or put them back.');

            return;
        }

        $this->resetValidation();
        $this->readMarks();
    }

    /**
     * Follow the setup when it adds or removes an assessment.
     */
    #[On('gradebook-changed')]
    public function followSetup(): void
    {
        if ($this->selectedItem() !== null && $this->changedStudentIds() !== []) {
            return;
        }

        if ($this->selectedItem() === null) {
            $this->gradeItemId = (string) ($this->items()->first()->id ?? '');
        }

        $this->readMarks();
    }

    /**
     * Throw away what was typed since the marks were read.
     */
    public function discard(): void
    {
        $this->resetValidation();
        $this->readMarks();
    }

    public function save(RecordGrade $recordGrade): void
    {
        Gate::authorize('manageGradebook', $this->courseOffering);
        $this->resetValidation();

        $item = $this->selectedItem();

        if ($item === null || !$this->acceptsMarks()) {
            $this->addError('sheet', 'This gradebook takes no marks now.');

            return;
        }

        try {
            $this->validate($this->rulesFor($item), [
                'marks.*.points.max' => 'A mark cannot be more than :max.',
                'marks.*.points.min' => 'A mark cannot be less than zero.',
                'marks.*.points.numeric' => 'Write the mark as a number.',
            ], [
                'marks.*.points' => 'mark',
                'marks.*.option' => 'grade',
                'marks.*.state' => 'state',
                'marks.*.comment' => 'comment',
            ]);
        } catch (ValidationException $exception) {
            // The row shows one message, so fold each field's onto its row.
            foreach ($exception->errors() as $key => $messages) {
                $this->addError((string) preg_replace('/^(marks\.\d+)\..+$/', '$1', $key), $messages[0]);
            }

            return;
        }

        $students = $this->students()->keyBy('id');
        $entries = $this->entriesFor($item);
        $saved = 0;

        foreach ($this->changedStudentIds() as $studentId) {
            $student = $students->get($studentId);

            if ($student === null) {
                continue;
            }

            $current = $this->valuesOf($entries->get($studentId));

            if ($current !== $this->original[$studentId] && !in_array($studentId, $this->overwrites, true)) {
                $this->original[$studentId] = $current;
                $this->overwrites[] = $studentId;
                $this->addError("marks.$studentId", 'Someone else changed this to '.$this->describe($item, $current).' since you opened the sheet. Save again to replace it.');

                continue;
            }

            $mark = $this->marks[$studentId];
            $state = GradeEntryState::from($mark['state']);

            try {
                $entry = $recordGrade->record(
                    $item,
                    $student,
                    $state,
                    $item->type === GradeItemType::Numeric && $mark['points'] !== '' ? (float) $mark['points'] : null,
                    $item->type === GradeItemType::Scale && $mark['option'] !== '' ? (int) $mark['option'] : null,
                    trim($mark['comment']) === '' ? null : trim($mark['comment']),
                    auth()->user(),
                );
            } catch (InvalidValueException $exception) {
                $this->addError("marks.$studentId", $exception->getMessage());

                continue;
            } catch (ClosedPeriodException $exception) {
                $this->addError('sheet', $exception->getMessage());

                return;
            }

            $this->marks[$studentId] = $this->original[$studentId] = $this->valuesOf($entry);
            $this->overwrites = array_values(array_diff($this->overwrites, [$studentId]));
            $saved++;
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->notify($saved === 0 ? 'Nothing was saved. Check the marks in red.' : "$saved saved. Check the marks in red.", 'danger');

            return;
        }

        $this->notify($saved === 0 ? 'Nothing changed.' : ($saved === 1 ? 'Mark saved.' : "$saved marks saved."));
    }

    /**
     * Send one learner's result for approval.
     */
    public function submitResult(int $studentId, PublishResult $publishResult): void
    {
        Gate::authorize('publishResult', $this->courseOffering);

        if (!$this->acceptsMarks()) {
            $this->notify('This gradebook takes no results now.', 'danger');

            return;
        }

        $student = $this->students()->firstWhere('id', $studentId);
        abort_if($student === null, 404);

        try {
            $publishResult->publish($this->courseOffering, $student, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('Sent for approval.');
    }

    public function approveResult(int $resultId, ApproveResult $approveResult): void
    {
        Gate::authorize('approveResult', $this->courseOffering);

        try {
            $approveResult->approve($this->result($resultId), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('Approved. The result is official now.');
    }

    public function startRejecting(int $resultId): void
    {
        Gate::authorize('approveResult', $this->courseOffering);

        $this->rejectingResultId = $this->result($resultId)->id;
        $this->rejectReason = '';
        $this->resetValidation('rejectReason');
    }

    public function stopRejecting(): void
    {
        $this->reset('rejectingResultId', 'rejectReason');
        $this->resetValidation('rejectReason');
    }

    public function rejectResult(RejectResult $rejectResult): void
    {
        Gate::authorize('approveResult', $this->courseOffering);
        abort_if($this->rejectingResultId === null, 404);

        $this->validate(['rejectReason' => ['required', 'string', 'max:500']], [
            'rejectReason.required' => 'Say what the teacher must change.',
        ]);

        try {
            $rejectResult->reject($this->result($this->rejectingResultId), auth()->user(), trim($this->rejectReason));
        } catch (InvalidValueException $exception) {
            $this->addError('rejectReason', $exception->getMessage());

            return;
        }

        $this->stopRejecting();
        $this->notify('Sent back to the teacher.');
    }

    public function render(): View
    {
        $students = $this->students();
        $studentIds = $this->studentIds();
        $item = $this->selectedItem();
        $results = ResultSnapshot::query()
            ->whereBelongsTo($this->courseOffering)
            ->whereIn('student_record_id', $studentIds)
            ->orderByDesc('revision')
            ->get()
            ->groupBy('student_record_id');

        return view('livewire.gradebook-mark-sheet', [
            'items' => $this->items()->loadCount(['entries' => fn ($query) => $query->whereIn('student_record_id', $studentIds)]),
            'item' => $item,
            'students' => $students,
            'states' => GradeEntryState::cases(),
            'officialResults' => $results->map(fn ($revisions) => $revisions->firstWhere('approval_status', ResultApprovalStatus::Approved)),
            'latestResults' => $results->map(fn ($revisions) => $revisions->first()),
            'canManage' => Gate::allows('manageGradebook', $this->courseOffering) && $this->acceptsMarks(),
            'canSubmit' => Gate::allows('publishResult', $this->courseOffering) && $this->acceptsMarks(),
            'canApprove' => Gate::allows('approveResult', $this->courseOffering),
            'changed' => $this->changedStudentIds(),
        ]);
    }

    /**
     * Read the marks of the open assessment afresh.
     */
    private function readMarks(): void
    {
        $item = $this->selectedItem();
        $entries = $item === null ? collect() : $this->entriesFor($item);

        $this->marks = [];

        foreach ($this->students() as $student) {
            $this->marks[$student->id] = $this->valuesOf($entries->get($student->id));
        }

        $this->original = $this->marks;
        $this->overwrites = [];
        $this->openItemId = $this->gradeItemId;
    }

    /**
     * @return list<int>
     */
    private function changedStudentIds(): array
    {
        return array_values(array_filter(
            array_map('intval', array_keys($this->marks)),
            fn (int $studentId): bool => isset($this->original[$studentId]) && $this->marks[$studentId] !== $this->original[$studentId],
        ));
    }

    /**
     * @return array{state: string, points: string, option: string, comment: string}
     */
    private function valuesOf(?GradeEntry $entry): array
    {
        return [
            'state' => ($entry->state ?? GradeEntryState::Graded)->value,
            'points' => $entry?->points === null ? '' : (string) $entry->points,
            'option' => $entry?->grading_scale_option_id === null ? '' : (string) $entry->grading_scale_option_id,
            'comment' => (string) $entry?->comment,
        ];
    }

    /**
     * Say what a mark holds, in the words the sheet shows.
     *
     * @param  array{state: string, points: string, option: string, comment: string}  $values
     */
    private function describe(GradeItem $item, array $values): string
    {
        $state = GradeEntryState::from($values['state']);

        if (!$state->needsPoints() && $item->type !== GradeItemType::Text) {
            return strtolower($state->label());
        }

        return match ($item->type) {
            GradeItemType::Scale => $item->gradingScale?->options->firstWhere('id', (int) $values['option'])->label ?? 'no grade',
            GradeItemType::Text => $values['comment'] === '' ? 'no comment' : '“'.str($values['comment'])->limit(40).'”',
            default => $values['points'] === '' ? 'no mark' : $values['points'],
        };
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rulesFor(GradeItem $item): array
    {
        return [
            'marks' => ['array'],
            'marks.*.state' => ['required', Rule::enum(GradeEntryState::class)],
            'marks.*.points' => $item->type === GradeItemType::Numeric
                ? ['nullable', 'numeric', 'min:0', ...($item->max_points === null ? [] : ['max:'.$item->max_points])]
                : ['nullable'],
            'marks.*.option' => $item->type === GradeItemType::Scale
                ? ['nullable', 'integer', Rule::in($item->gradingScale?->options->modelKeys() ?? [])]
                : ['nullable'],
            'marks.*.comment' => ['nullable', 'string', 'max:5000'],
        ];
    }

    private function acceptsMarks(): bool
    {
        return $this->courseOffering->academicPeriod?->status->acceptsWrites() ?? false;
    }

    private function selectedItem(): ?GradeItem
    {
        return $this->gradeItemId === '' || !ctype_digit($this->gradeItemId)
            ? null
            : $this->items()->firstWhere('id', (int) $this->gradeItemId);
    }

    /**
     * @return Collection<int, GradeItem>
     */
    private function items(): Collection
    {
        return once(fn (): Collection => $this->courseOffering->gradeItems()
            ->with(['category:id,name', 'gradingScale:id,name', 'gradingScale.options:id,grading_scale_id,label,points,position'])
            ->orderBy('position')
            ->orderBy('id')
            ->get());
    }

    /**
     * @return SupportCollection<int, StudentRecord>
     */
    private function students(): SupportCollection
    {
        return once(fn (): SupportCollection => app(CourseOfferingRoster::class)->students($this->courseOffering));
    }

    /**
     * @return list<int>
     */
    private function studentIds(): array
    {
        return $this->students()->pluck('id')->map(fn ($id): int => (int) $id)->values()->all();
    }

    /**
     * @return Collection<int, GradeEntry>
     */
    private function entriesFor(GradeItem $item): Collection
    {
        return $item->entries()->whereIn('student_record_id', $this->studentIds())->get()->keyBy('student_record_id');
    }

    private function result(int $resultId): ResultSnapshot
    {
        return $this->courseOffering->resultSnapshots()->findOrFail($resultId);
    }
}
