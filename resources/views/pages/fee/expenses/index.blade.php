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
    @if ($expenses->isEmpty())
        <p class="py-8 text-sm text-muted-foreground">No expenses yet</p>
    @else
        <ul class="divide-y border-y" aria-label="Expenses">
            @foreach ($expenses as $expense)
                <li class="flex items-center justify-between gap-4 py-3">
                    <div class="min-w-0 text-sm">
                        <p class="truncate font-medium">{{ $expense->description }}</p>
                        <p class="truncate text-muted-foreground">{{ $expense->expense_date->format('j M Y') }} · {{ $expense->vendor ?: '—' }} · {{ $expense->account?->name ?? '—' }} · {{ str($expense->method)->replace('_', ' ')->ucfirst() }}</p>
                    </div>
                    <span class="shrink-0 text-sm font-medium tabular-nums">{{ money_text($expense->amount) }}</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $expenses->links('components.pagination-links-view') }}</div>
    @endif
@endsection
