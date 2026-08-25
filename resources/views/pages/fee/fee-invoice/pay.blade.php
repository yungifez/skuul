@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('fee-invoices.index'), 'text' => 'Fee invoices'],
    ['href' => route('fee-invoices.show', $feeInvoice->id), 'text' => $feeInvoice->name],
    ['href' => route('fee-invoices.pay', $feeInvoice->id), 'text' => 'Take payment', 'active'],
]])

@section('title', __('Take a payment'))

@section('page_heading', __('Take a payment'))

@section('content')
@php
    $locale = app()->getLocale();
    $outstanding = $feeInvoice->balance;
    $openLines = $feeInvoice->feeInvoiceRecords->filter(fn ($line) => $line->outstanding->isPositive());
@endphp

<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $feeInvoice->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $feeInvoice->user?->name }} &middot; due {{ $feeInvoice->due_date?->format('j M Y') }}.
            What a fee still owes is worked out from the payments themselves, so two people taking money at
            once can never leave the invoice saying the wrong thing.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase text-muted-foreground">Charged</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight">{{ $feeInvoice->amount->plus($feeInvoice->fine)->minus($feeInvoice->waiver)->formatToLocale($locale) }}</p>
        </div>
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase text-muted-foreground">Paid</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight">{{ $feeInvoice->paid->formatToLocale($locale) }}</p>
        </div>
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase text-muted-foreground">Still owed</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight {{ $outstanding->isPositive() ? 'text-foreground' : 'text-muted-foreground' }}">
                {{ $outstanding->formatToLocale($locale) }}
            </p>
        </div>
    </div>

    @if ($openLines->isEmpty())
        <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-sidebar-border/70 p-10 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                <x-lucide-check class="size-6" />
            </span>
            <p class="text-sm font-medium">This invoice is settled.</p>
            <p class="max-w-sm text-sm text-muted-foreground">
                Money taken now would be held as credit for the student instead. Record it on the student's account.
            </p>
            @if ($feeInvoice->user?->studentRecord !== null)
                <april:button-link href="{{ route('student-accounts.show', $feeInvoice->user->studentRecord->id) }}" variant="outline">
                    Open the student account
                </april:button-link>
            @endif
        </div>
    @else
        <form action="{{ route('fee-invoices.pay.store', $feeInvoice->id) }}" method="POST"
            x-data="{ spread: '{{ old('spread', 'oldest_first') }}' }">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">What arrived</h3>
                    <p class="text-sm text-muted-foreground">Record the money first. Which fees it clears is the next question.</p>
                </div>

                <div class="flex flex-col gap-6 p-6">
                    <x-display-validation-errors />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="payment-amount" class="text-sm font-medium leading-none">Amount</label>
                            <input id="payment-amount" name="amount" type="number" step="0.01" min="0.01" required
                                value="{{ old('amount') }}" autocomplete="off"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                            <p class="text-xs text-muted-foreground">Anything above what this invoice owes is kept as credit for the student.</p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="payment-received-on" class="text-sm font-medium leading-none">Received on</label>
                            <input id="payment-received-on" name="received_on" type="date" max="{{ now()->toDateString() }}"
                                value="{{ old('received_on', now()->toDateString()) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>
                    </div>

                    <fieldset class="flex flex-col gap-3">
                        <legend class="text-sm font-medium leading-none">How did the money reach the school?</legend>

                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($channels as $key => $channel)
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-input p-4 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                                    <input type="radio" name="method" value="{{ $key }}" class="mt-1 size-4"
                                        @if (old('method', 'cash') === $key) checked @endif>
                                    <span class="flex flex-col gap-1">
                                        <span class="text-sm font-medium leading-none">{{ $channel->label() }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $channel->description() }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="payment-reference" class="text-sm font-medium leading-none">Reference <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="payment-reference" name="reference" maxlength="100" value="{{ old('reference') }}" autocomplete="off"
                                placeholder="Teller number, cheque number, or transfer reference"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="payment-note" class="text-sm font-medium leading-none">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="payment-note" name="note" maxlength="1000" value="{{ old('note') }}" autocomplete="off"
                                placeholder="Anything the office should remember"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 border-y bg-muted/30 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Which fees does it clear?</h3>
                    <p class="text-sm text-muted-foreground">Leave this alone unless the family asked for a particular fee.</p>
                </div>

                <div class="flex flex-col gap-4 p-6">
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-input p-4 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                        <input type="radio" name="spread" value="oldest_first" class="mt-1 size-4" x-model="spread">
                        <span class="flex flex-col gap-1">
                            <span class="text-sm font-medium leading-none">Clear the oldest fees first</span>
                            <span class="text-xs text-muted-foreground">The usual choice. The money runs down the list until it is used up.</span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-input p-4 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                        <input type="radio" name="spread" value="by_line" class="mt-1 size-4" x-model="spread">
                        <span class="flex flex-col gap-1">
                            <span class="text-sm font-medium leading-none">Name the fees myself</span>
                            <span class="text-xs text-muted-foreground">Say how much goes against each fee. No fee can be given more than it owes.</span>
                        </span>
                    </label>

                    <div class="overflow-x-auto" x-show="spread === 'by_line'" x-cloak>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                                    <th class="py-2 pr-4 font-medium">Fee</th>
                                    <th class="py-2 pr-4 font-medium">Charged</th>
                                    <th class="py-2 pr-4 font-medium">Paid</th>
                                    <th class="py-2 pr-4 font-medium">Still owed</th>
                                    <th class="py-2 font-medium">Put against it</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($openLines as $line)
                                    <tr class="border-b last:border-0">
                                        <td class="py-3 pr-4 font-medium">{{ $line->fee?->name }}</td>
                                        <td class="py-3 pr-4 text-muted-foreground">{{ $line->payable->formatToLocale($locale) }}</td>
                                        <td class="py-3 pr-4 text-muted-foreground">{{ $line->paid->formatToLocale($locale) }}</td>
                                        <td class="py-3 pr-4">{{ $line->outstanding->formatToLocale($locale) }}</td>
                                        <td class="py-3">
                                            <input type="number" step="0.01" min="0"
                                                name="lines[{{ $line->id }}]" value="{{ old('lines.'.$line->id) }}"
                                                max="{{ $line->outstanding->getAmount()->toFloat() }}"
                                                aria-label="Amount against {{ $line->fee?->name }}"
                                                class="flex h-9 w-32 rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t p-6 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('fee-invoices.show', $feeInvoice->id) }}"
                        class="inline-flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground">
                        Back to the invoice
                    </a>
                    <april:button type="submit" class="w-full sm:w-auto">
                        <x-lucide-credit-card class="mr-2 size-4" />
                        Record the payment
                    </april:button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
