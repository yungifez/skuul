<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\FeeInvoice;
use App\Models\FinancialPeriod;
use App\Services\Finance\FinancialPeriodResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListFeeInvoicesTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected $queryString = ['status', 'financialPeriodId'];

    /** @var array<int, string> */
    public array $statuses = ['all', 'due', 'paid'];

    public string $status = 'due';

    public ?int $financialPeriodId = null;

    public function mount(): void
    {
        parent::mount();
        $this->financialPeriodId ??= app(FinancialPeriodResolver::class)->currentOpen(current_school_id())?->id;
        $this->status = in_array($this->status, $this->statuses, true) ? $this->status : 'due';
    }

    public function updatedStatus(): void
    {
        if (!in_array($this->status, $this->statuses, true)) {
            $this->status = 'all';
        }

        $this->resetPage();
    }

    public function updatedFinancialPeriodId(): void
    {
        $this->resetPage();
    }

    protected function builder(): Builder
    {
        $user = auth()->user();
        $query = FeeInvoice::query()
            ->ofSchool()
            ->when($this->financialPeriodId !== null, fn (Builder $query) => $query->where('financial_period_id', $this->financialPeriodId))
            ->orderByDesc('due_date')
            ->with(['user', 'studentRecord.academicCycleSection.academicLevel']);

        if ($user->hasRole('parent')) {
            $query->whereRelation('studentRecord.user.parents', 'parent_records.user_id', $user->id);
        } elseif ($user->hasRole('student')) {
            $query->where('student_record_id', $user->studentRecord?->id);
        }

        return match ($this->status) {
            'due' => $query->isDue(),
            'paid' => $query->isPaid(),
            default => $query,
        };
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        $columns = [
            Column::make('Invoice', 'name')->searchable()->sortable(),
            Column::make('Student', 'student_name'),
        ];

        if (!auth()->user()->hasRole('student')) {
            $columns[] = Column::make(school_term('class_level', 'Class'), 'class_name');
            $columns[] = Column::make(school_term('section', 'Section'), 'section_name');
        }

        return array_merge($columns, [
            Column::make('Paid', 'paid_label'),
            Column::make('Balance', 'balance_label'),
            Column::make('Due date', 'due_date_label')->sortable(),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (FeeInvoice $invoice): array {
            $row = $invoice->toArray();
            $row['student_name'] = $invoice->user->name;

            $studentRecord = $invoice->studentRecord;
            $className = '—';
            $sectionName = '—';

            if ($studentRecord !== null) {
                $cycleSection = $studentRecord->academicCycleSection;

                if ($cycleSection !== null) {
                    $className = $cycleSection->academicLevel->name;
                    $sectionName = $cycleSection->name;
                }
            }

            $row['class_name'] = $className;
            $row['section_name'] = $sectionName;
            $row['paid_label'] = $invoice->paid->formatToLocale(app()->getLocale());
            $row['balance_label'] = $invoice->balance->formatToLocale(app()->getLocale());
            $row['due_date_label'] = $invoice->due_date->format('M j, Y');
            $row['view_url'] = route('fee-invoices.show', $invoice);
            $row['edit_url'] = route('fee-invoices.edit', $invoice);
            $row['pay_url'] = route('fee-invoices.pay', $invoice);
            $row['delete_url'] = route('fee-invoices.destroy', $invoice);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-fee-invoices-table', array_merge($this->aprilTablePayload(), [
            'financialPeriods' => FinancialPeriod::query()->inSchool()->orderByDesc('starts_on')->get(),
            'canManageInvoices' => auth()->user()->can('update fee invoice'),
            'canPayInvoices' => auth()->user()->can('update fee invoice'),
            'canDeleteInvoices' => auth()->user()->can('delete fee invoice'),
        ]));
    }
}
