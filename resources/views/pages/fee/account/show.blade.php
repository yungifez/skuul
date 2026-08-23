@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('fee-invoices.index'), 'text' => 'Fee invoices'],
    ['href' => route('student-accounts.show', $enrollment->id), 'text' => 'Student account', 'active'],
]])

@section('title', __('Student account'))

@section('page_heading', __('Student account'))

@section('content')
@php
    $locale = app()->getLocale();
    $owes = $balance > 0;
@endphp

<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $enrollment->user?->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $enrollment->admission_number }}
            @if ($enrollment->academicCycleSection !== null)
                &middot; {{ $enrollment->academicCycleSection->academicLevel?->name }} {{ $enrollment->academicCycleSection->name }}
            @endif
            &middot; every figure below is worked out from the payments and the books, never from a stored total.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Owed to the school</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight {{ $owes ? 'text-foreground' : 'text-muted-foreground' }}">
                {{ number_format($balance, 2) }}
            </p>
            <p class="mt-1 text-xs text-muted-foreground">{{ $owes ? 'This family still has fees to pay.' : 'Nothing is outstanding.' }}</p>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Held for this student</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ $credit->formatToLocale($locale) }}</p>
            <p class="mt-1 text-xs text-muted-foreground">Money paid that no invoice has used yet.</p>

            @if ($credit->isPositive() && $canTakeMoney)
                <form action="{{ route('student-accounts.apply-credit', $enrollment->id) }}" method="POST" class="mt-4">
                    @csrf
                    <april:button type="submit" variant="outline" class="w-full sm:w-auto">
                        <x-lucide-arrow-down-left class="mr-2 size-4" />
                        Use it against fees owed
                    </april:button>
                </form>
            @endif
        </div>
    </div>

    @if ($elsewhere->isNotEmpty())
        <div class="rounded-xl border border-amber-500/40 bg-amber-500/5 p-5 text-sm">
            <p class="font-medium">This learner also has money on the books at another campus.</p>
            <p class="mt-1 text-muted-foreground">
                Those campuses keep separate books, so the money is theirs to collect. It is not added to the figure
                above and it cannot be paid here.
            </p>
            <ul class="mt-3 flex flex-col gap-1">
                @foreach ($elsewhere as $row)
                    <li class="flex items-center justify-between gap-4">
                        <span>{{ $row['school']->name }}</span>
                        <span class="font-medium">{{ number_format($row['balance'], 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Invoices</h3>
            <p class="text-sm text-muted-foreground">What the school has charged, and how much of each bill is left.</p>
        </div>

        @if ($invoices->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-file-text class="size-6" />
                </span>
                <p class="text-sm font-medium">No invoices yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Invoice</th>
                            <th class="p-4 font-medium">Due</th>
                            <th class="p-4 font-medium">Charged</th>
                            <th class="p-4 font-medium">Paid</th>
                            <th class="p-4 font-medium">Still owed</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-medium">
                                    <a href="{{ route('fee-invoices.show', $invoice->id) }}" class="hover:underline">{{ $invoice->name }}</a>
                                </td>
                                <td class="p-4 text-muted-foreground">{{ $invoice->due_date?->format('j M Y') }}</td>
                                <td class="p-4 text-muted-foreground">{{ $invoice->amount->plus($invoice->fine)->minus($invoice->waiver)->formatToLocale($locale) }}</td>
                                <td class="p-4 text-muted-foreground">{{ $invoice->paid->formatToLocale($locale) }}</td>
                                <td class="p-4">{{ $invoice->balance->formatToLocale($locale) }}</td>
                                <td class="p-4 text-right">
                                    @if ($canTakeMoney && $invoice->balance->isPositive())
                                        <april:button-link href="{{ route('fee-invoices.pay', $invoice->id) }}" variant="outline" size="sm">
                                            Take payment
                                        </april:button-link>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Payments</h3>
            <p class="text-sm text-muted-foreground">Every payment stays on this list. A mistake is taken back, never rubbed out.</p>
        </div>

        @if ($payments->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-receipt class="size-6" />
                </span>
                <p class="text-sm font-medium">No money has been taken yet.</p>
            </div>
        @else
            <ul class="divide-y">
                @foreach ($payments as $payment)
                    <li class="flex flex-col gap-3 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex flex-col gap-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-base font-semibold tracking-tight">{{ $payment->amount->formatToLocale($locale) }}</span>
                                <span class="text-xs text-muted-foreground">{{ $payment->methodLabel() }}</span>
                                @if ($payment->isReversal())
                                    <april:badge variant="destructive">Reversal</april:badge>
                                @elseif ($payment->isReversed())
                                    <april:badge variant="outline">Taken back</april:badge>
                                @endif
                            </div>
                            <p class="text-sm text-muted-foreground">
                                {{ $payment->received_on?->format('j M Y') }}
                                @if ($payment->reference)
                                    &middot; {{ $payment->reference }}
                                @endif
                                @if ($payment->recordedBy !== null)
                                    &middot; recorded by {{ $payment->recordedBy->name }}
                                @endif
                            </p>
                            @if ($payment->note)
                                <p class="text-sm text-muted-foreground">{{ $payment->note }}</p>
                            @endif
                            @if ($payment->allocations->isNotEmpty())
                                <p class="text-xs text-muted-foreground">
                                    Put against
                                    {{ $payment->allocations->map(fn ($allocation) => $allocation->feeInvoice?->name)->filter()->unique()->join(', ') }}
                                </p>
                            @endif
                        </div>

                        @if ($canRefund && !$payment->isReversal() && !$payment->isReversed() && $payment->amount->isPositive())
                            <form action="{{ route('student-payments.reverse', $payment->id) }}" method="POST"
                                class="flex w-full flex-col gap-2 sm:w-72">
                                @csrf
                                <input name="reason" maxlength="500" required placeholder="Why is it being taken back?"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                <april:button type="submit" variant="outline" size="sm">Take this payment back</april:button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($canRefund)
        <form action="{{ route('student-accounts.refund', $enrollment->id) }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Give money back</h3>
                    <p class="text-sm text-muted-foreground">
                        Only money the school is holding can be given back. A family that still owes fees is not refunded by mistake.
                    </p>
                </div>

                @if (!$credit->isPositive())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                            <x-lucide-lock class="size-6" />
                        </span>
                        <p class="text-sm font-medium">There is nothing to give back.</p>
                        <p class="max-w-sm text-sm text-muted-foreground">The school holds no unused money for this student.</p>
                    </div>
                @else
                    <div class="grid gap-4 p-6 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="refund-amount" class="text-sm font-medium leading-none">Amount</label>
                            <input id="refund-amount" name="amount" type="number" step="0.01" min="0.01" required
                                max="{{ $credit->getAmount()->toFloat() }}" value="{{ old('amount') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                            <p class="text-xs text-muted-foreground">At most {{ $credit->formatToLocale($locale) }}.</p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="refund-method" class="text-sm font-medium leading-none">How is it being paid out?</label>
                            <select id="refund-method" name="method"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                @foreach ($channels as $key => $channel)
                                    <option value="{{ $key }}">{{ $channel->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2 sm:col-span-2">
                            <label for="refund-reason" class="text-sm font-medium leading-none">Reason</label>
                            <input id="refund-reason" name="reason" maxlength="500" required value="{{ old('reason') }}"
                                placeholder="Why the money is going back to the family"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="flex flex-col gap-2 sm:col-span-2">
                            <label for="refund-reference" class="text-sm font-medium leading-none">Reference <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="refund-reference" name="reference" maxlength="100" value="{{ old('reference') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>
                    </div>

                    <div class="flex justify-end border-t p-6">
                        <april:button type="submit" variant="outline">
                            <x-lucide-undo-2 class="mr-2 size-4" />
                            Record the refund
                        </april:button>
                    </div>
                @endif
            </div>
        </form>
    @endif
</div>
@endsection
