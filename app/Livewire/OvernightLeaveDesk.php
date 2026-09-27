<?php

namespace App\Livewire;

use App\Actions\Boarding\DecideOvernightLeave;
use App\Actions\Boarding\RequestOvernightLeave;
use App\Enums\OvernightLeaveStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\BoardingPlace;
use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Nights a boarder spends away from the house.
 *
 * A learner stays on this screen until somebody records them back. A night
 * away that ended without that record is overdue and is listed first.
 */
class OvernightLeaveDesk extends Component
{
    use DispatchesStatusNotifications;

    public bool $isAsking = false;

    public string $learnerId = '';

    public string $leavesOn = '';

    public string $returnsOn = '';

    public string $destination = '';

    public string $contact = '';

    public string $reason = '';

    #[Locked]
    public ?int $refusingId = null;

    public string $refuseNote = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', OvernightLeave::class);

        $this->resetAskForm();
    }

    public function startAsking(): void
    {
        $this->resetAskForm();
        $this->isAsking = true;
    }

    public function stopAsking(): void
    {
        $this->resetAskForm();
    }

    public function ask(RequestOvernightLeave $requestOvernightLeave): void
    {
        Gate::authorize('create', OvernightLeave::class);

        $this->validate([
            'learnerId' => ['required', 'integer', Rule::in($this->boarderIds())],
            'leavesOn' => ['required', 'date_format:Y-m-d', 'date'],
            'returnsOn' => ['required', 'date_format:Y-m-d', 'date', 'after_or_equal:leavesOn'],
            'destination' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'learnerId.required' => 'Choose the boarder.',
            'learnerId.in' => 'This learner does not board now.',
            'destination.required' => 'Say where the learner is going.',
            'returnsOn.after_or_equal' => 'A learner cannot come back before they leave.',
        ], [
            'learnerId' => 'boarder',
            'leavesOn' => 'leaving day',
            'returnsOn' => 'return day',
        ]);

        try {
            $requestOvernightLeave->request(
                enrollment: StudentRecord::query()->inSchool()->findOrFail((int) $this->learnerId),
                leavesOn: $this->leavesOn,
                returnsOn: $this->returnsOn,
                destination: trim($this->destination),
                contact: trim($this->contact) === '' ? null : trim($this->contact),
                reason: trim($this->reason) === '' ? null : trim($this->reason),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('leavesOn', $exception->getMessage());

            return;
        }

        $this->resetAskForm();
        $this->notify('The request is waiting for a decision.');
    }

    public function approve(int $leaveId, DecideOvernightLeave $decideOvernightLeave): void
    {
        $this->answer($leaveId, OvernightLeaveStatus::Approved, null, $decideOvernightLeave, 'Approved.');
    }

    public function startRefusing(int $leaveId): void
    {
        Gate::authorize('decide', $this->leave($leaveId));

        $this->resetValidation();
        $this->refusingId = $leaveId;
        $this->refuseNote = '';
    }

    public function stopRefusing(): void
    {
        $this->reset('refusingId', 'refuseNote');
        $this->resetValidation();
    }

    public function refuse(DecideOvernightLeave $decideOvernightLeave): void
    {
        if ($this->refusingId === null) {
            return;
        }

        $this->validate(['refuseNote' => ['required', 'string', 'max:1000']], [
            'refuseNote.required' => 'Say why, so the house can tell the family.',
        ]);

        $this->answer($this->refusingId, OvernightLeaveStatus::Refused, trim($this->refuseNote), $decideOvernightLeave, 'Refused.');
        $this->reset('refusingId', 'refuseNote');
    }

    public function cancel(int $leaveId, DecideOvernightLeave $decideOvernightLeave): void
    {
        $this->answer($leaveId, OvernightLeaveStatus::Cancelled, null, $decideOvernightLeave, 'Cancelled.');
    }

    public function markReturned(int $leaveId, DecideOvernightLeave $decideOvernightLeave): void
    {
        $this->answer($leaveId, OvernightLeaveStatus::Returned, null, $decideOvernightLeave, 'Back in the house.');
    }

    public function render(): View
    {
        $today = today()->toDateString();
        $with = ['studentRecord:id,user_id,admission_number', 'studentRecord.user:id,name'];

        return view('livewire.overnight-leave-desk', [
            'overdue' => OvernightLeave::query()->inSchool()->where('status', OvernightLeaveStatus::Approved)->where('returns_on', '<', $today)->with($with)->orderBy('returns_on')->get(),
            'tonight' => OvernightLeave::query()->inSchool()->awayOn($today)->with($with)->orderBy('returns_on')->get(),
            'upcoming' => OvernightLeave::query()->inSchool()->where('status', OvernightLeaveStatus::Approved)->where('leaves_on', '>', $today)->with($with)->orderBy('leaves_on')->get(),
            'waiting' => OvernightLeave::query()->inSchool()->waiting()->with($with)->orderBy('leaves_on')->get(),
            'recent' => OvernightLeave::query()->inSchool()->whereIn('status', [OvernightLeaveStatus::Refused, OvernightLeaveStatus::Cancelled, OvernightLeaveStatus::Returned])
                ->with([...$with, 'decidedBy:id,name'])
                ->orderByDesc('id')
                ->limit(15)
                ->get(),
            'boarders' => $this->isAsking ? $this->boarders() : collect(),
            'canDecide' => Gate::allows('decide overnight leave'),
            'canAsk' => Gate::allows('create', OvernightLeave::class),
            'today' => today(),
        ]);
    }

    /**
     * Move one request on, and say plainly when somebody got there first.
     */
    private function answer(int $leaveId, OvernightLeaveStatus $status, ?string $note, DecideOvernightLeave $decideOvernightLeave, string $done): void
    {
        $leave = $this->leave($leaveId);

        // The house that asked may call the night off; only a decider answers.
        if ($status === OvernightLeaveStatus::Cancelled && Gate::allows('create', OvernightLeave::class)) {
            Gate::authorize('view', $leave);
        } else {
            Gate::authorize('decide', $leave);
        }

        try {
            $decideOvernightLeave->decide($leave, $status, $note, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($done);
    }

    private function leave(int $leaveId): OvernightLeave
    {
        return OvernightLeave::query()->inSchool()->findOrFail($leaveId);
    }

    /**
     * @return list<int>
     */
    private function boarderIds(): array
    {
        return $this->boarders()->pluck('id')->all();
    }

    /**
     * The learners of this school who sleep in a house now.
     *
     * @return Collection<int, StudentRecord>
     */
    private function boarders(): Collection
    {
        $boardingIds = BoardingPlace::query()
            ->current()
            ->where('school_id', current_school_id())
            ->whereNotNull('dormitory_bed_id')
            ->pluck('student_record_id');

        return StudentRecord::query()
            ->inSchool()
            ->whereIn('id', $boardingIds)
            ->with('user:id,name')
            ->get(['id', 'user_id', 'admission_number'])
            ->sortBy(fn (StudentRecord $learner): string => mb_strtolower($learner->user->name ?? (string) $learner->admission_number))
            ->values()
            ->toBase();
    }

    private function resetAskForm(): void
    {
        $this->reset('isAsking', 'learnerId', 'destination', 'contact', 'reason');
        $this->leavesOn = today()->toDateString();
        $this->returnsOn = today()->addDay()->toDateString();
        $this->resetValidation();
    }
}
