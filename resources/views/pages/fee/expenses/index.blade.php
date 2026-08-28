@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['text' => 'Expenses', 'active'],
]])

@section('title', 'Expenses')
@section('page_heading', 'Expenses')

@section('page_actions')
    <x-resource-create-action :href="route('expenses.create')" ability="create" :arguments="[\App\Models\Expense::class]">Record expense</x-resource-create-action>
@endsection

@section('content')
    <april:card>
        <slot:title>Recorded expenses</slot:title>
        <slot:description>Money already spent by the school. Posted expenses stay in the books.</slot:description>
        <slot:content>
            @if ($expenses->isEmpty())
                <p class="py-8 text-sm text-muted-foreground">No expenses have been recorded yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b text-muted-foreground"><th class="p-3">Date</th><th class="p-3">Description</th><th class="p-3">Account</th><th class="p-3">Paid from</th><th class="p-3 text-right">Amount</th></tr></thead>
                        <tbody class="divide-y">
                            @foreach ($expenses as $expense)
                                <tr><td class="p-3">{{ $expense->expense_date->format('j M Y') }}</td><td class="p-3"><div class="font-medium">{{ $expense->description }}</div><div class="text-xs text-muted-foreground">{{ $expense->vendor ?: 'No vendor recorded' }}</div></td><td class="p-3">{{ $expense->account?->name }}</td><td class="p-3">{{ str($expense->method)->replace('_', ' ')->title() }}</td><td class="p-3 text-right">{{ number_format($expense->amount, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $expenses->links() }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
