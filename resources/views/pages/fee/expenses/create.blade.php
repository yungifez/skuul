@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['href' => route('expenses.index'), 'text' => 'Expenses'],
    ['text' => 'Record expense', 'active'],
]])

@section('title', 'Record expense')
@section('page_heading', 'Record expense')

@section('content')
    <form method="POST" action="{{ route('expenses.store') }}" class="max-w-3xl space-y-6">
        @csrf
        <april:card>
            <slot:title>What was spent?</slot:title>
            <slot:description>Use one entry for each payment. This keeps reports easy to read.</slot:description>
            <slot:content>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-2 sm:col-span-2"><april:label for="description">Description</april:label><april:input id="description" name="description" value="{{ old('description') }}" required placeholder="Teaching supplies" /><x-field-error name="description" /></div>
                    <div class="flex flex-col gap-2"><april:label for="amount">Amount</april:label><april:input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" required placeholder="0.00" /><x-field-error name="amount" /></div>
                    <div class="flex flex-col gap-2"><april:label for="expense_date">Date</april:label><april:input id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', now()->toDateString()) }}" required /><x-field-error name="expense_date" /></div>
                    <div class="flex flex-col gap-2"><april:label for="ledger_account_id">Expense account</april:label><april:select id="ledger_account_id" name="ledger_account_id" required><option value="">Choose an account</option>@foreach ($accounts as $account)<option value="{{ $account->id }}" @selected(old('ledger_account_id') == $account->id)>{{ $account->code }} · {{ $account->name }}</option>@endforeach</april:select><x-field-error name="ledger_account_id" /></div>
                    <div class="flex flex-col gap-2"><april:label for="method">Paid from</april:label><april:select id="method" name="method" required>@foreach ($channels as $channel)<option value="{{ $channel->key() }}" @selected(old('method', 'cash') === $channel->key())>{{ $channel->label() }}</option>@endforeach</april:select><x-field-error name="method" /></div>
                    <div class="flex flex-col gap-2"><april:label for="vendor">Vendor (optional)</april:label><april:input id="vendor" name="vendor" value="{{ old('vendor') }}" placeholder="Who was paid?" /><x-field-error name="vendor" /></div>
                    <div class="flex flex-col gap-2"><april:label for="reference">Reference (optional)</april:label><april:input id="reference" name="reference" value="{{ old('reference') }}" placeholder="Receipt or transfer number" /><x-field-error name="reference" /></div>
                    <div class="flex flex-col gap-2"><april:label for="program_id">Programme (optional)</april:label><april:select id="program_id" name="program_id"><option value="">All programmes</option>@foreach ($programs as $program)<option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>{{ $program->name }}</option>@endforeach</april:select></div>
                    <div class="flex flex-col gap-2"><april:label for="fund">Fund (optional)</april:label><april:input id="fund" name="fund" value="{{ old('fund') }}" placeholder="Fund or grant" /></div>
                    <div class="flex flex-col gap-2 sm:col-span-2"><april:label for="note">Note (optional)</april:label><april:textarea id="note" name="note" placeholder="Additional context">{{ old('note') }}</april:textarea></div>
                </div>
            </slot:content>
        </april:card>
        <div class="flex flex-wrap justify-end gap-3"><april:button-link href="{{ route('fee-invoices.index') }}" variant="outline">Cancel</april:button-link><april:button type="submit">Record expense</april:button></div>
    </form>
@endsection
