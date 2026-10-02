<?php

namespace App\Livewire;

use App\Actions\Finance\RecordExpense;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\RecordsOnce;
use App\Models\Expense;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\PaymentChannelRegistry;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Record money the school has already spent.
 *
 * The form shows what the account the money came from holds in the books. An
 * expense above that needs a second, explicit yes, because it is usually a
 * typing mistake. A transfer, card or cheque needs its reference, so the
 * payment can be found on the bank statement later.
 */
class RecordExpenseForm extends Component
{
    use RecordsOnce;

    public string $description = '';

    public string $amount = '';

    public string $expenseDate = '';

    public string $ledgerAccountId = '';

    public string $method = 'cash';

    public string $vendor = '';

    public string $reference = '';

    public string $programId = '';

    public string $fund = '';

    public string $note = '';

    public bool $confirmsMoreThanBalance = false;

    public bool $isAboveBalance = false;

    public function mount(): void
    {
        Gate::authorize('create', Expense::class);

        $this->expenseDate = school_today()->toDateString();
    }

    public function updatedAmount(): void
    {
        $this->reset('isAboveBalance', 'confirmsMoreThanBalance');
    }

    public function updatedMethod(): void
    {
        $this->reset('isAboveBalance', 'confirmsMoreThanBalance');
    }

    public function save(RecordExpense $recordExpense, PaymentChannelRegistry $channels, ChartOfAccounts $chart): void
    {
        Gate::authorize('create', Expense::class);

        $channel = $channels->has($this->method) ? $channels->get($this->method) : null;

        $this->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:1000000000', 'decimal:0,2'],
            'expenseDate' => ['required', 'date', 'before_or_equal:'.school_today()->toDateString()],
            'ledgerAccountId' => ['required', 'integer', Rule::exists('ledger_accounts', 'id')->where(fn (Builder $query) => $query
                ->where('school_id', current_school_id())
                ->where('type', 'expense')
                ->where('is_active', true))],
            'method' => ['required', Rule::in($channels->keys())],
            'vendor' => ['nullable', 'string', 'max:150'],
            'reference' => [$channel?->needsReference() ? 'required' : 'nullable', 'string', 'max:100'],
            'programId' => ['nullable', 'integer', Rule::exists('programs', 'id')->where(fn (Builder $query) => $query->where('school_id', current_school_id()))],
            'fund' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'expenseDate.before_or_equal' => 'An expense cannot be dated in the future.',
            'ledgerAccountId.required' => 'Choose what the money was spent on.',
            'reference.required' => 'Add the reference from the bank statement or slip.',
        ], [
            'expenseDate' => 'date',
            'ledgerAccountId' => 'expense account',
            'programId' => 'programme',
        ]);

        $amount = round((float) $this->amount, 2);
        $source = $chart->account($channel->accountPurpose());
        $balance = $source->balance();

        if ($amount > $balance && !$this->confirmsMoreThanBalance) {
            $this->isAboveBalance = true;
            $this->addError('amount', "{$source->name} holds ".money_text($balance).' in the books. Check the amount, or confirm below.');

            return;
        }

        try {
            // A save sent again writes nothing and goes back to the list.
            $this->recordOnce('expense', fn () => $recordExpense->record(
                account: LedgerAccount::query()->inSchool()->findOrFail((int) $this->ledgerAccountId),
                amount: $amount,
                description: trim($this->description),
                method: $this->method,
                date: Carbon::parse($this->expenseDate),
                vendor: $this->filled($this->vendor),
                reference: $this->filled($this->reference),
                note: $this->filled($this->note),
                program: $this->programId === '' ? null : Program::query()->inSchool()->findOrFail((int) $this->programId),
                fund: $this->filled($this->fund),
            ));
        } catch (InvalidValueException $exception) {
            $this->addError('expenseDate', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Expense recorded.');
        $this->redirectRoute('expenses.index');
    }

    public function render(PaymentChannelRegistry $channels, ChartOfAccounts $chart): View
    {
        $channel = $channels->has($this->method) ? $channels->get($this->method) : null;
        $source = $channel === null ? null : $chart->account($channel->accountPurpose());

        return view('livewire.record-expense-form', [
            'accounts' => LedgerAccount::query()->inSchool()->where('type', 'expense')->where('is_active', true)->orderBy('code')->get(),
            'programs' => Program::query()->inSchool()->orderBy('name')->get(),
            'channels' => $channels->all(),
            'needsReference' => $channel?->needsReference() ?? false,
            'source' => $source,
            'balance' => $source?->balance(),
        ]);
    }

    private function filled(string $value): ?string
    {
        return trim($value) === '' ? null : trim($value);
    }
}
