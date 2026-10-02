<div class="mx-auto flex w-full max-w-5xl flex-col gap-10">
    @php
        $locale = app()->getLocale();
        $owes = $balance > 0;
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section aria-label="Account summary" class="flex flex-col gap-6">
        <p class="flex flex-wrap items-center gap-x-2 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ $enrollment->user?->name }}</span>
            <span>· {{ $enrollment->admission_number }}</span>
            @if ($enrollment->academicCycleSection !== null)
                <span>· {{ $enrollment->academicCycleSection->academicLevel?->name }} {{ $enrollment->academicCycleSection->label ?? $enrollment->academicCycleSection->name }}</span>
            @endif
        </p>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4">
            <div>
                <dt class="text-sm text-muted-foreground">Owed</dt>
                <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-muted-foreground' => !$owes]) id="account-owed">{{ money_text($balance) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Credit held</dt>
                <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-muted-foreground' => !$credit->isPositive()]) id="account-credit">{{ $credit->formatToLocale($locale) }}</dd>
            </div>
        </dl>

        @if ($elsewhere->isNotEmpty())
            <ul class="flex flex-col gap-1 text-sm" aria-label="Owed at other campuses">
                @foreach ($elsewhere as $row)
                    <li class="flex items-center justify-between gap-4">
                        <span class="text-muted-foreground">Owed at {{ $row['school']->name }}</span>
                        <span class="font-medium tabular-nums">{{ money_text($row['balance']) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($credit->isPositive() && ($canTakeMoney || $canRefund))
            <div class="flex flex-col gap-3 sm:flex-row">
                @if ($canTakeMoney && $owes)
                    <april:button type="button" class="h-11 select-none" wire:click="applyCredit" wire:loading.attr="disabled" wire:target="applyCredit">Use credit against fees</april:button>
                @endif
                @if ($canRefund && !$isRefunding)
                    <april:button type="button" variant="outline" class="h-11 select-none" wire:click="$set('isRefunding', true)">Give money back</april:button>
                @endif
            </div>
            <x-field-error name="credit" />
        @endif

        @if ($canRefund && $isRefunding && $credit->isPositive())
            <form wire:submit="refund" class="flex flex-col gap-3" aria-labelledby="refund-heading">
                <h2 id="refund-heading" class="text-base font-semibold">Give money back <span class="font-normal text-muted-foreground">· up to {{ $credit->formatToLocale($locale) }}</span></h2>
                <div class="grid gap-3 sm:grid-cols-[10rem_12rem_1fr]">
                    <div>
                        <label for="refund-amount" class="sr-only">Amount</label>
                        <input type="number" id="refund-amount" wire:model="refundAmount" step="0.01" min="0.01" max="{{ $credit->getAmount()->toFloat() }}" placeholder="Amount" class="{{ $controlClasses }}" {{ field_error_bindings('refundAmount') }}>
                    </div>
                    <div>
                        <label for="refund-method" class="sr-only">Paid out by</label>
                        <select id="refund-method" wire:model="refundMethod" class="{{ $controlClasses }}" {{ field_error_bindings('refundMethod') }}>
                            @foreach ($channels as $key => $channel)
                                <option value="{{ $key }}">{{ $channel->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="refund-reference" class="sr-only">Reference</label>
                        <input id="refund-reference" wire:model="refundReference" maxlength="100" placeholder="Reference (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('refundReference') }}>
                    </div>
                </div>
                <div>
                    <label for="refund-reason" class="sr-only">Reason</label>
                    <input id="refund-reason" wire:model="refundReason" maxlength="500" placeholder="Why the money is going back" class="{{ $controlClasses }}" {{ field_error_bindings('refundReason') }}>
                </div>
                <x-field-error name="refundAmount" />
                <x-field-error name="refundMethod" />
                <x-field-error name="refundReference" />
                <x-field-error name="refundReason" />
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="refund"
                        wire:confirm="Record this refund? It cannot be edited afterwards.">Record the refund</april:button>
                </div>
            </form>
        @endif
    </section>

    <section aria-labelledby="invoices-heading" class="flex flex-col gap-3">
        <h2 id="invoices-heading" class="text-base font-semibold">Invoices</h2>
        @if ($invoices->isEmpty())
            <p class="text-sm text-muted-foreground">No invoices</p>
        @else
            @php
                $relievable = fn ($invoice) => $canRefund && $invoice->ledger_transaction_id !== null && $invoice->balance->isPositive();
                $anyRelievable = $invoices->contains($relievable);
                $relievingInvoice = $relievingInvoiceId === null ? null : $invoices->firstWhere('id', $relievingInvoiceId);
            @endphp
            <div class="relative overflow-x-auto border-y">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="py-3 pr-4 font-medium">Invoice</th>
                            <th class="py-3 pr-4 font-medium">Due</th>
                            <th class="py-3 pr-4 text-right font-medium">Charged</th>
                            <th class="py-3 pr-4 text-right font-medium">Paid</th>
                            <th class="py-3 pr-4 text-right font-medium">Owed</th>
                            <th class="py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($invoices as $invoice)
                            <tr wire:key="invoice-{{ $invoice->id }}">
                                <td class="py-3 pr-4 font-medium">
                                    <a href="{{ route('fee-invoices.show', $invoice->id) }}" class="hover:underline">{{ $invoice->name }}</a>
                                </td>
                                <td class="whitespace-nowrap py-3 pr-4 text-muted-foreground">{{ $invoice->due_date?->format('j M Y') ?? '—' }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums text-muted-foreground">{{ $invoice->amount->plus($invoice->fine)->minus($invoice->waiver)->formatToLocale($locale) }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums text-muted-foreground">{{ $invoice->paid->formatToLocale($locale) }}</td>
                                <td @class(['whitespace-nowrap py-3 pr-4 text-right tabular-nums', 'font-medium' => $invoice->balance->isPositive(), 'text-muted-foreground' => !$invoice->balance->isPositive()])>{{ $invoice->balance->formatToLocale($locale) }}</td>
                                <td class="py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($canTakeMoney && $invoice->balance->isPositive())
                                            <april:button-link href="{{ route('fee-invoices.pay', $invoice->id) }}" variant="outline" class="h-11 select-none whitespace-nowrap">Take payment</april:button-link>
                                        @endif
                                        @if ($relievable($invoice))
                                            <april:dropdown-menu>
                                                <slot:trigger>
                                                    <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" aria-label="More for {{ $invoice->name }}">
                                                        <x-lucide-ellipsis class="size-4" />
                                                    </april:button>
                                                </slot:trigger>
                                                <slot:content>
                                                    <april:dropdown-menu-item wire:click="startRelieving({{ $invoice->id }})">
                                                        <x-lucide-badge-minus class="mr-2 size-4" />Waive or write off
                                                    </april:dropdown-menu-item>
                                                </slot:content>
                                            </april:dropdown-menu>
                                        @elseif ($anyRelievable)
                                            <span class="size-11 shrink-0" aria-hidden="true"></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($canRefund && $relievingInvoice !== null)
                @php
                    $owedLines = $relievingInvoice->feeInvoiceRecords->filter(fn ($line) => $line->outstanding->isPositive());
                @endphp
                <form wire:submit="relieve" wire:key="relief-{{ $relievingInvoice->id }}" class="flex flex-col gap-3" aria-labelledby="relief-heading">
                    <h3 id="relief-heading" class="text-sm font-semibold">Waive or write off <span class="font-normal text-muted-foreground">· {{ $relievingInvoice->name }}</span></h3>
                    <div class="grid gap-3 sm:grid-cols-[1fr_10rem_12rem]">
                        <div>
                            <label for="relief-line" class="sr-only">Fee</label>
                            <select id="relief-line" wire:model="reliefLineId" class="{{ $controlClasses }}" {{ field_error_bindings('reliefLineId') }}>
                                <option value="">Choose the fee</option>
                                @foreach ($owedLines as $line)
                                    <option value="{{ $line->id }}">{{ $line->fee?->name ?? 'Fee' }} · {{ $line->outstanding->formatToLocale($locale) }} owed</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="relief-amount" class="sr-only">Amount</label>
                            <input type="number" id="relief-amount" wire:model="reliefAmount" step="0.01" min="0.01" placeholder="Amount" class="{{ $controlClasses }}" {{ field_error_bindings('reliefAmount') }}>
                        </div>
                        <div>
                            <label for="relief-kind" class="sr-only">Kind</label>
                            <select id="relief-kind" wire:model="reliefKind" class="{{ $controlClasses }}" {{ field_error_bindings('reliefKind') }}>
                                <option value="waiver">Waiver or scholarship</option>
                                <option value="write_off">Write-off, cannot collect</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="relief-reason" class="sr-only">Reason</label>
                        <input id="relief-reason" wire:model="reliefReason" maxlength="500" placeholder="Why the fee is being taken off" class="{{ $controlClasses }}" {{ field_error_bindings('reliefReason') }}>
                    </div>
                    <x-field-error name="reliefLineId" />
                    <x-field-error name="reliefAmount" />
                    <x-field-error name="reliefKind" />
                    <x-field-error name="reliefReason" />
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="relieve"
                            wire:confirm="Take this off the invoice? It is kept in the books and cannot be edited afterwards.">Take it off</april:button>
                    </div>
                </form>
            @endif
        @endif
    </section>

    <section aria-labelledby="payments-heading" class="flex flex-col gap-3">
        <h2 id="payments-heading" class="text-base font-semibold">Payments</h2>
        @if ($payments->isEmpty())
            <p class="text-sm text-muted-foreground">No payments</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($payments as $payment)
                    @php
                        $canReverse = $canRefund && $payment->school_id === $campusId && !$payment->isReversal() && !$payment->isReversed() && $payment->amount->isPositive();
                    @endphp
                    <li wire:key="payment-{{ $payment->id }}" class="flex flex-col gap-3 py-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 text-sm">
                                <p class="flex flex-wrap items-baseline gap-x-2">
                                    <span @class(['font-semibold tabular-nums', 'line-through text-muted-foreground' => $payment->isReversed()])>{{ $payment->amount->formatToLocale($locale) }}</span>
                                    <span class="text-muted-foreground">{{ $payment->methodLabel() }}</span>
                                    @if ($payment->isReversal())
                                        <span class="text-destructive">Reversal</span>
                                    @elseif ($payment->isReversed())
                                        <span class="text-muted-foreground">Taken back</span>
                                    @endif
                                </p>
                                <p class="text-muted-foreground">
                                    {{ $payment->received_on?->format('j M Y') }}
                                    @if ($payment->reference)
                                        · {{ $payment->reference }}
                                    @endif
                                    @if ($payment->recordedBy !== null)
                                        · {{ $payment->recordedBy->name }}
                                    @endif
                                </p>
                                @if ($payment->allocations->isNotEmpty())
                                    <p class="text-muted-foreground">For {{ $payment->allocations->map(fn ($allocation) => $allocation->feeInvoice?->name)->filter()->unique()->join(', ') }}</p>
                                @endif
                                @if ($payment->note)
                                    <p class="text-muted-foreground">{{ $payment->note }}</p>
                                @endif
                            </div>
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" aria-label="Actions for the payment of {{ $payment->amount->formatToLocale($locale) }} on {{ $payment->received_on?->format('j M Y') }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content>
                                    <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('student-payments.receipt', $payment) }}'">
                                        <x-lucide-receipt class="mr-2 size-4" />Receipt
                                    </april:dropdown-menu-item>
                                    @if ($canReverse)
                                        <april:dropdown-menu-item wire:click="startReversing({{ $payment->id }})">
                                            <x-lucide-undo-2 class="mr-2 size-4" />Take back
                                        </april:dropdown-menu-item>
                                    @endif
                                </slot:content>
                            </april:dropdown-menu>
                        </div>

                        @if ($canReverse && $reversingPaymentId === $payment->id)
                            <form wire:submit="reversePayment" class="flex flex-col gap-3 sm:flex-row sm:items-start" aria-label="Take back this payment">
                                <div class="min-w-0 flex-1">
                                    <label for="reverse-reason" class="sr-only">Why</label>
                                    <input id="reverse-reason" wire:model="reverseReason" maxlength="500" placeholder="Why it is being taken back" class="{{ $controlClasses }}" {{ field_error_bindings('reverseReason') }}>
                                    <x-field-error name="reverseReason" class="mt-1" />
                                </div>
                                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                                <april:button type="submit" variant="destructive" class="h-11 select-none" wire:loading.attr="disabled" wire:target="reversePayment">Take back</april:button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
