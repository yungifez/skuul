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
    <april:card>
        <slot:title>Cash moved to bank</slot:title>
        <slot:description>Each deposit moves money from the cash box to the bank account in the books.</slot:description>
        <slot:content>
            @if ($deposits->isEmpty())
                <p class="py-8 text-sm text-muted-foreground">No cash deposits recorded yet.</p>
            @else
                <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b text-muted-foreground"><th class="p-3">Date</th><th class="p-3">Bank reference</th><th class="p-3">Period</th><th class="p-3 text-right">Amount</th></tr></thead><tbody class="divide-y">@foreach ($deposits as $deposit)<tr><td class="p-3">{{ $deposit->deposit_date->format('j M Y') }}</td><td class="p-3">{{ $deposit->bank_reference ?: '—' }}</td><td class="p-3">{{ $deposit->financialPeriod?->name }}</td><td class="p-3 text-right">{{ number_format($deposit->amount, 2) }}</td></tr>@endforeach</tbody></table></div><div class="mt-4">{{ $deposits->links() }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
