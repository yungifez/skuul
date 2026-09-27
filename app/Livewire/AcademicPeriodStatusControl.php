<?php

namespace App\Livewire;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Move a school year or one of its periods through closing and reopening.
 *
 * Closing runs the readiness check first. When it finds outstanding work, the
 * findings show and the person may close anyway on purpose.
 */
class AcademicPeriodStatusControl extends Component
{
    public AcademicYear|AcademicPeriod $period;

    public bool $showStatus = true;

    /** The form that is open: "close", "reopen", or none. */
    public ?string $step = null;

    public string $reason = '';

    public bool $force = false;

    /** Whether the last close was refused over outstanding work. */
    public bool $isBlocked = false;

    public function beginClosing(ChangeAcademicPeriodStatus $changeAcademicPeriodStatus): void
    {
        Gate::authorize('close', $this->period);

        try {
            $changeAcademicPeriodStatus->beginClosing($this->period, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('reason', $exception->getMessage());

            return;
        }

        $this->finish('Now closing. Finish the checklist, then close.');
    }

    public function close(ChangeAcademicPeriodStatus $changeAcademicPeriodStatus): void
    {
        Gate::authorize('close', $this->period);

        $this->validate(['reason' => ['nullable', 'string', 'max:500']], attributes: ['reason' => 'note']);

        try {
            $changeAcademicPeriodStatus->close($this->period, auth()->user(), $this->reasonOrNull(), $this->force);
        } catch (InvalidValueException $exception) {
            $this->isBlocked = true;
            $this->addError('reason', $exception->getMessage());

            return;
        }

        $this->finish('Closed.');
    }

    public function reopen(ChangeAcademicPeriodStatus $changeAcademicPeriodStatus): void
    {
        Gate::authorize('reopen', $this->period);

        $this->validate(['reason' => ['required', 'string', 'max:500']], ['reason.required' => 'Say why it is reopening.']);

        try {
            $changeAcademicPeriodStatus->reopen($this->period, auth()->user(), $this->reason);
        } catch (InvalidValueException $exception) {
            $this->addError('reason', $exception->getMessage());

            return;
        }

        $this->finish('Reopened.');
    }

    public function open(string $step): void
    {
        $this->reset('reason', 'force', 'isBlocked');
        $this->resetErrorBag();
        $this->step = in_array($step, ['close', 'reopen'], true) ? $step : null;
    }

    public function cancel(): void
    {
        $this->reset('step', 'reason', 'force', 'isBlocked');
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $status = $this->period->status;

        return view('livewire.academic-period-status-control', [
            'status' => $status,
            'periodName' => $this->period instanceof AcademicYear ? $this->period->name : $this->period->displayName,
            'canBeginClosing' => $status === AcademicPeriodStatus::Open && Gate::allows('close', $this->period),
            'canClose' => $status === AcademicPeriodStatus::Closing && Gate::allows('close', $this->period),
            'canReopen' => $status === AcademicPeriodStatus::Closed && Gate::allows('reopen', $this->period),
        ]);
    }

    private function finish(string $message): void
    {
        session()->flash('success', $message);
        $this->redirect(url()->previous(), navigate: false);
    }

    private function reasonOrNull(): ?string
    {
        return trim($this->reason) === '' ? null : trim($this->reason);
    }
}
