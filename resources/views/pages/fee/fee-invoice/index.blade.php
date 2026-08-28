@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Finance', 'active'],
]])

@section('title', 'Finance')
@section('page_heading', 'Finance')

@section('page_actions')
    <div class="flex flex-wrap gap-2">
        <april:button-link href="{{ route('expenses.create') }}" variant="outline">Record expense</april:button-link>
        <x-resource-create-action :href="route('fee-invoices.create')" ability="create" :arguments="[\App\Models\FeeInvoice::class]">Add invoice</x-resource-create-action>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-muted-foreground">A simple view of what families owe, what the school received, and what it spent.</p>
            @if ($period)
                <p class="mt-1 text-sm text-muted-foreground">Showing {{ $period->name }}. Financial period: {{ $period->isClosed() ? 'closed' : 'open' }}.</p>
            @else
                <p class="mt-1 text-sm text-destructive">Create a financial period before recording invoices, payments, or expenses.</p>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <april:card><slot:title>Outstanding</slot:title><slot:description>Unpaid invoices in this period</slot:description><slot:content><p class="text-2xl font-semibold">{{ number_format($summary['outstanding'], 2) }}</p></slot:content></april:card>
            <april:card><slot:title>Overdue invoices</slot:title><slot:description>Invoices past their due date</slot:description><slot:content><p class="text-2xl font-semibold">{{ $summary['overdue'] }}</p></slot:content></april:card>
            <april:card><slot:title>Received</slot:title><slot:description>Payments recorded in this period</slot:description><slot:content><p class="text-2xl font-semibold">{{ number_format($summary['received'], 2) }}</p></slot:content></april:card>
            <april:card><slot:title>Spent</slot:title><slot:description>Expenses recorded in this period</slot:description><slot:content><p class="text-2xl font-semibold">{{ number_format($summary['spent'], 2) }}</p></slot:content></april:card>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(18rem,1fr)]">
            <div class="space-y-6">@livewire('list-fee-invoices-table', ['financialPeriodId' => $period?->id])</div>

            <div class="space-y-6">
                <april:card>
                    <slot:title>Finance tasks</slot:title>
                    <slot:description>Common office actions</slot:description>
                    <slot:content>
                        <div class="flex flex-col gap-2">
                            <april:button-link href="{{ route('fee-invoices.create') }}" variant="outline" class="justify-start">Create an invoice</april:button-link>
                            <april:button-link href="{{ route('fees.index') }}" variant="outline" class="justify-start">Manage fees</april:button-link>
                            <april:button-link href="{{ route('fee-categories.index') }}" variant="outline" class="justify-start">Manage fee categories</april:button-link>
                            <april:button-link href="{{ route('expenses.index') }}" variant="outline" class="justify-start">Review expenses</april:button-link>
                            <april:button-link href="{{ route('cash-deposits.index') }}" variant="outline" class="justify-start">Review cash deposits</april:button-link>
                            <april:button-link href="{{ route('budgets.index') }}" variant="outline" class="justify-start">Plan a budget</april:button-link>
                            <april:button-link href="{{ route('reports.index') }}" variant="outline" class="justify-start">Open reports</april:button-link>
                        </div>
                    </slot:content>
                </april:card>

                <april:card>
                    <slot:title>Financial periods</slot:title>
                    <slot:description>Posting stops when a period is closed. This does not change academic terms.</slot:description>
                    <slot:content>
                        <div class="space-y-3">
                            @forelse ($financialPeriods as $financialPeriod)
                                <div class="flex items-start justify-between gap-3 rounded-lg border p-3">
                                    <div><p class="font-medium">{{ $financialPeriod->name }}</p><p class="text-xs text-muted-foreground">{{ $financialPeriod->starts_on->format('j M Y') }} – {{ $financialPeriod->ends_on->format('j M Y') }}</p></div>
                                    @if ($financialPeriod->isClosed())
                                        <form method="POST" action="{{ route('financial-periods.reopen', $financialPeriod) }}">@csrf<input type="hidden" name="reason" value="Period reopened by finance administrator"><april:button type="submit" variant="outline" size="sm">Reopen</april:button></form>
                                    @else
                                        <form method="POST" action="{{ route('financial-periods.close', $financialPeriod) }}">@csrf<input type="hidden" name="reason" value="Period closed by finance administrator"><april:button type="submit" variant="outline" size="sm">Close</april:button></form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-muted-foreground">No financial periods yet.</p>
                            @endforelse
                        </div>
                        @can('manage financial period')
                            <form method="POST" action="{{ route('financial-periods.store') }}" class="mt-5 space-y-3 border-t pt-5">@csrf<p class="text-sm font-medium">Add a period</p><april:input name="name" required placeholder="2026 financial year" value="{{ old('name') }}" /><div class="grid gap-3 sm:grid-cols-2"><april:input name="starts_on" type="date" required value="{{ old('starts_on') }}" /><april:input name="ends_on" type="date" required value="{{ old('ends_on') }}" /></div><april:button type="submit" variant="outline">Add period</april:button></form>
                        @endcan
                    </slot:content>
                </april:card>
            </div>
        </div>
    </div>
@endsection
