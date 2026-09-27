@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Finance', 'active'],
]])

@section('title', 'Finance')
@section('page_heading', 'Finance')

@section('page_actions')
    @php
        $financeLinks = collect([
            ['can' => Gate::allows('create', \App\Models\Expense::class), 'href' => route('expenses.create'), 'icon' => 'receipt', 'text' => 'Record expense'],
            ['can' => Gate::allows('viewAny', \App\Models\Fee::class), 'href' => route('fees.index'), 'icon' => 'list', 'text' => 'Fees'],
            ['can' => Gate::allows('viewAny', \App\Models\FeeCategory::class), 'href' => route('fee-categories.index'), 'icon' => 'folder', 'text' => 'Fee categories'],
            ['can' => Gate::allows('viewAny', \App\Models\Expense::class), 'href' => route('expenses.index'), 'icon' => 'wallet', 'text' => 'Expenses'],
            ['can' => Gate::allows('viewAny', \App\Models\CashDeposit::class), 'href' => route('cash-deposits.index'), 'icon' => 'landmark', 'text' => 'Cash deposits'],
            ['can' => Gate::allows('viewAny', \App\Models\Budget::class), 'href' => route('budgets.index'), 'icon' => 'piggy-bank', 'text' => 'Budgets'],
            ['can' => Gate::allows('viewAny', \App\Models\ReportRun::class), 'href' => route('reports.index'), 'icon' => 'chart-column', 'text' => 'Reports'],
        ])->where('can', true);
    @endphp
    <div class="flex items-center gap-2">
        <x-resource-create-action :href="route('fee-invoices.create')" ability="create" :arguments="[\App\Models\FeeInvoice::class]">Add invoice</x-resource-create-action>
        @if ($financeLinks->isNotEmpty())
            <april:dropdown-menu>
                <slot:trigger>
                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More finance pages">
                        <x-lucide-ellipsis class="size-4" />
                    </april:button>
                </slot:trigger>
                <slot:content align="end" class="w-52">
                    @foreach ($financeLinks as $link)
                        <april:dropdown-menu-item x-on:click="window.location.href = '{{ $link['href'] }}'"><x-dynamic-component :component="'lucide-'.$link['icon']" class="mr-2 size-4" />{{ $link['text'] }}</april:dropdown-menu-item>
                    @endforeach
                </slot:content>
            </april:dropdown-menu>
        @endif
    </div>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-10">
        <section aria-label="Finance summary" class="flex flex-col gap-4">
            @if ($period)
                <p class="text-sm text-muted-foreground">{{ $period->name }}@if ($period->isClosed()) · Closed @endif</p>
            @else
                <p class="text-sm text-destructive">Add a financial period before recording invoices, payments or expenses.</p>
            @endif

            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
                <div>
                    <dt class="text-sm text-muted-foreground">Owed</dt>
                    <dd class="text-2xl font-semibold tabular-nums tracking-tight" id="finance-owed">{{ money_text($summary['outstanding']) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Overdue invoices</dt>
                    <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-destructive' => $summary['overdue'] > 0, 'text-muted-foreground' => $summary['overdue'] === 0])>{{ $summary['overdue'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Received</dt>
                    <dd class="text-2xl font-semibold tabular-nums tracking-tight">{{ money_text($summary['received']) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Spent</dt>
                    <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-muted-foreground' => $summary['spent'] == 0])>{{ money_text($summary['spent']) }}</dd>
                </div>
            </dl>
        </section>

        <section class="min-w-0">
            @livewire('list-fee-invoices-table', ['financialPeriodId' => $period?->id])
        </section>

        <section>
            <livewire:manage-financial-periods />
        </section>
    </div>
@endsection
