<?php

namespace App\Livewire;

use App\Actions\Finance\ChangeFinancialPeriodStatus;
use App\Enums\FinancialPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\FinancialPeriod;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * List the school's financial periods, and let the bursar add, close and reopen them.
 *
 * Closing a period stops new entries being posted to it. It does not touch
 * the academic calendar.
 */
class ManageFinancialPeriods extends Component
{
    use DispatchesStatusNotifications;

    public bool $isAdding = false;

    public string $name = '';

    public string $startsOn = '';

    public string $endsOn = '';

    public function save(): void
    {
        $this->mustManage();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('financial_periods', 'name')->where('school_id', current_school_id())],
            'startsOn' => ['required', 'date'],
            'endsOn' => ['required', 'date', 'after_or_equal:startsOn'],
        ], attributes: ['startsOn' => 'start date', 'endsOn' => 'end date']);

        FinancialPeriod::create([
            'name' => $validated['name'],
            'starts_on' => $validated['startsOn'],
            'ends_on' => $validated['endsOn'],
            'school_id' => current_school_id(),
        ]);

        $this->reset('isAdding', 'name', 'startsOn', 'endsOn');
        $this->notify('Financial period added.');
    }

    public function close(int $periodId, ChangeFinancialPeriodStatus $change): void
    {
        $this->changeStatus($periodId, FinancialPeriodStatus::Closed, 'Period closed by finance administrator', $change);
    }

    public function reopen(int $periodId, ChangeFinancialPeriodStatus $change): void
    {
        $this->changeStatus($periodId, FinancialPeriodStatus::Open, 'Period reopened by finance administrator', $change);
    }

    public function cancel(): void
    {
        $this->reset('isAdding', 'name', 'startsOn', 'endsOn');
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.manage-financial-periods', [
            'periods' => FinancialPeriod::query()->inSchool()->orderByDesc('starts_on')->get(),
            'canManage' => auth()->user()?->can('manage financial period') === true,
        ]);
    }

    private function changeStatus(int $periodId, FinancialPeriodStatus $status, string $reason, ChangeFinancialPeriodStatus $change): void
    {
        $this->mustManage();

        $period = FinancialPeriod::query()->inSchool()->findOrFail($periodId);

        try {
            $change->change($period, $status, $reason);
        } catch (InvalidValueException $exception) {
            $this->addError('period', $exception->getMessage());

            return;
        }

        $this->notify($status === FinancialPeriodStatus::Closed ? "{$period->name} closed." : "{$period->name} reopened.");
    }

    private function mustManage(): void
    {
        abort_unless(auth()->user()?->can('manage financial period') === true, 403);
    }
}
