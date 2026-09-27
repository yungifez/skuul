@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['text' => 'Cash deposits', 'active'],
]])

@section('title', 'Cash deposits')
@section('page_heading', 'Cash deposits')

@section('page_actions')
    <april:button-link href="{{ route('cash-deposits.create') }}">Record cash deposit</april:button-link>
@endsection

@section('content')
    @if ($deposits->isEmpty())
        <p class="py-8 text-sm text-muted-foreground">No cash deposits yet</p>
    @else
        <ul class="divide-y border-y" aria-label="Cash deposits">
            @foreach ($deposits as $deposit)
                <li class="flex items-center justify-between gap-4 py-3">
                    <div class="min-w-0 text-sm">
                        <p class="font-medium">{{ $deposit->deposit_date->format('j M Y') }}</p>
                        <p class="truncate text-muted-foreground">{{ $deposit->bank_reference ?: '—' }} · {{ $deposit->financialPeriod?->name ?? '—' }}</p>
                    </div>
                    <span class="shrink-0 text-sm font-medium tabular-nums">{{ money_text($deposit->amount) }}</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $deposits->links('components.pagination-links-view') }}</div>
    @endif
@endsection
