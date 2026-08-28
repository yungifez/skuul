@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['href' => route('cash-deposits.index'), 'text' => 'Cash deposits'],
    ['text' => 'Record cash deposit', 'active'],
]])

@section('title', 'Record cash deposit')
@section('page_heading', 'Record cash deposit')

@section('content')
    <form method="POST" action="{{ route('cash-deposits.store') }}" class="max-w-xl space-y-6">@csrf<april:card><slot:title>Move cash to the bank</slot:title><slot:description>Record this after the money has reached the school bank account.</slot:description><slot:content><div class="space-y-4"><div class="flex flex-col gap-2"><april:label for="amount">Amount</april:label><april:input id="amount" name="amount" type="number" step="0.01" min="0.01" required value="{{ old('amount') }}" placeholder="0.00" />@error('amount')<p class="text-sm text-destructive">{{ $message }}</p>@enderror</div><div class="flex flex-col gap-2"><april:label for="deposit_date">Date</april:label><april:input id="deposit_date" name="deposit_date" type="date" required value="{{ old('deposit_date', now()->toDateString()) }}" />@error('deposit_date')<p class="text-sm text-destructive">{{ $message }}</p>@enderror</div><div class="flex flex-col gap-2"><april:label for="bank_reference">Bank reference (optional)</april:label><april:input id="bank_reference" name="bank_reference" value="{{ old('bank_reference') }}" placeholder="Deposit slip or statement reference" /></div><div class="flex flex-col gap-2"><april:label for="note">Note (optional)</april:label><april:textarea id="note" name="note">{{ old('note') }}</april:textarea></div></div></slot:content></april:card><div class="flex justify-end gap-3"><april:button-link href="{{ route('fee-invoices.index') }}" variant="outline">Cancel</april:button-link><april:button type="submit">Record cash deposit</april:button></div></form>
@endsection
