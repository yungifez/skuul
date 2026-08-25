<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\FeeInvoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListFeeInvoicesTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected $queryString = ['status'];

    /** @var array<int, string> */
    public array $statuses = ['all', 'due', 'paid'];

    public string $status = 'due';

    public int $year;

    public function mount(): void
    {
        parent::mount();
        $this->year = (int) date('Y');
        $this->status = in_array($this->status, $this->statuses, true) ? $this->status : 'due';
    }

    public function updatedStatus(): void
    {
        if (!in_array($this->status, $this->statuses, true)) {
            $this->status = 'all';
        }

        $this->resetPage();
    }

    public function updatedYear(): void
    {
        $this->resetPage();
    }

    protected function builder(): Builder
    {
        $user = auth()->user();
        $query = FeeInvoice::query()->whereYear('due_date', $this->year)->orderByDesc('due_date')->with(['user.studentRecord.academicCycleSection.academicLevel']);

        if ($user->hasRole('parent')) {
            $query->ofSchool()->whereRelation('user.parents', 'parent_records.user_id', $user->id);
        } elseif ($user->hasRole('student')) {
            $query->whereRelation('user', 'id', $user->id);
        } else {
            $query->ofSchool();
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
            $row['class_name'] = $invoice->user->studentRecord->academicCycleSection->academicLevel->name ?? '—';
            $row['section_name'] = $invoice->user->studentRecord->academicCycleSection->name ?? '—';
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
            'canManageInvoices' => !auth()->user()->hasAnyRole(['student', 'parent']),
            'canPayInvoices' => !auth()->user()->hasAnyRole(['student', 'parent']),
            'canDeleteInvoices' => auth()->user()->can('delete fee invoice'),
        ]));
    }
}
