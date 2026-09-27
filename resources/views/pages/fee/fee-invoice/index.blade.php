@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Finance', 'active'],
]])

@section('title', 'Finance')
@section('page_heading', 'Finance')

@section('page_actions')
    <div class="flex items-center gap-2">
        <x-resource-create-action :href="route('fee-invoices.create')" ability="create" :arguments="[\App\Models\FeeInvoice::class]">Add invoice</x-resource-create-action>
        <april:dropdown-menu>
            <slot:trigger>
                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More finance pages">
                    <x-lucide-ellipsis class="size-4" />
                </april:button>
            </slot:trigger>
            <slot:content align="end" class="w-52">
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('expenses.create') }}'"><x-lucide-receipt class="mr-2 size-4" />Record expense</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('fees.index') }}'"><x-lucide-list class="mr-2 size-4" />Fees</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('fee-categories.index') }}'"><x-lucide-folder class="mr-2 size-4" />Fee categories</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('expenses.index') }}'"><x-lucide-wallet class="mr-2 size-4" />Expenses</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('cash-deposits.index') }}'"><x-lucide-landmark class="mr-2 size-4" />Cash deposits</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('budgets.index') }}'"><x-lucide-piggy-bank class="mr-2 size-4" />Budgets</april:dropdown-menu-item>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('reports.index') }}'"><x-lucide-chart-column class="mr-2 size-4" />Reports</april:dropdown-menu-item>
            </slot:content>
        </april:dropdown-menu>
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
