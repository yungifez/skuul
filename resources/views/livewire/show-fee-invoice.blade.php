<div class="mx-auto flex w-full max-w-5xl flex-col gap-10">
    @php
        $locale = app()->getLocale();
        $dash = '—';
        $section = $feeInvoice->studentRecord?->academicCycleSection;
        $charged = $feeInvoice->amount->plus($feeInvoice->fine)->minus($feeInvoice->waiver);
        $state = match (true) {
            !$hasLines => 'No fees',
            $isSettled => 'Paid',
            $isOverdue => 'Overdue',
            $feeInvoice->paid->isPositive() => 'Part paid',
            default => 'Not paid',
        };
    @endphp

    <section aria-label="Invoice summary" class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
                @if ($canViewAccount && $feeInvoice->studentRecord !== null)
                    <a href="{{ route('student-accounts.show', $feeInvoice->studentRecord) }}" class="font-medium text-foreground hover:underline">{{ $feeInvoice->user?->name }}</a>
                @else
                    <span class="font-medium text-foreground">{{ $feeInvoice->user?->name }}</span>
                @endif
                @if ($feeInvoice->studentRecord?->admission_number)
                    <span>· {{ $feeInvoice->studentRecord->admission_number }}</span>
                @endif
                @if ($section !== null)
                    <span>· {{ $section->academicLevel?->name }} {{ $section->label ?? $section->name }}</span>
                @endif
                <april:badge variant="{{ $isOverdue ? 'destructive' : 'outline' }}" id="invoice-state">
                    @if ($isSettled)
                        <x-lucide-check class="mr-1 size-3" aria-hidden="true" />
                    @endif
                    {{ $state }}
                </april:badge>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($isSettled)
                    <april:button-link href="{{ route('fee-invoices.print', $feeInvoice) }}" class="hidden h-11 select-none sm:inline-flex">
                        <x-lucide-printer class="mr-2 size-4" />Print receipt
                    </april:button-link>
                @elseif ($canTakeMoney && $hasLines && $feeInvoice->studentRecord !== null)
                    <april:button-link href="{{ route('fee-invoices.pay', $feeInvoice) }}" class="hidden h-11 select-none sm:inline-flex">Take payment</april:button-link>
                @endif
                <april:dropdown-menu>
                    <slot:trigger>
                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $feeInvoice->name }}">
                            <x-lucide-ellipsis class="size-4" />
                        </april:button>
                    </slot:trigger>
                    <slot:content>
                        @unless ($isSettled)
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('fee-invoices.print', $feeInvoice) }}'">
                                <x-lucide-printer class="mr-2 size-4" />Print invoice
                            </april:dropdown-menu-item>
                        @endunless
                        @if ($canTakeMoney)
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('fee-invoices.edit', $feeInvoice) }}'">
                                <x-lucide-pencil class="mr-2 size-4" />Edit
                            </april:dropdown-menu-item>
                        @endif
                        @if ($canViewAccount && $feeInvoice->studentRecord !== null)
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('student-accounts.show', $feeInvoice->studentRecord) }}'">
                                <x-lucide-wallet class="mr-2 size-4" />Student account
                            </april:dropdown-menu-item>
                        @endif
                    </slot:content>
                </april:dropdown-menu>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">Issued</dt>
                <dd class="font-medium">{{ $feeInvoice->issue_date?->format('j M Y') ?? $dash }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Due</dt>
                <dd @class(['font-medium', 'text-destructive' => $isOverdue])>{{ $feeInvoice->due_date?->format('j M Y') ?? $dash }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Charged</dt>
                <dd class="font-medium tabular-nums">{{ $charged->formatToLocale($locale) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Owed</dt>
                <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-muted-foreground' => !$feeInvoice->balance->isPositive()]) id="invoice-owed">{{ $feeInvoice->balance->formatToLocale($locale) }}</dd>
            </div>
        </dl>

        @if (filled($feeInvoice->note))
            <p class="text-sm whitespace-pre-line text-muted-foreground">{{ $feeInvoice->note }}</p>
        @endif

        @if ($isSettled)
            <april:button-link href="{{ route('fee-invoices.print', $feeInvoice) }}" class="h-11 w-full select-none sm:hidden">
                <x-lucide-printer class="mr-2 size-4" />Print receipt
            </april:button-link>
        @elseif ($canTakeMoney && $hasLines && $feeInvoice->studentRecord !== null)
            <april:button-link href="{{ route('fee-invoices.pay', $feeInvoice) }}" class="h-11 w-full select-none sm:hidden">Take payment</april:button-link>
        @endif
    </section>

    <section aria-labelledby="fees-heading" class="flex flex-col gap-3">
        <h2 id="fees-heading" class="text-base font-semibold">Fees</h2>
        @if (!$hasLines)
            <p class="text-sm text-muted-foreground">No fees are on this invoice yet.</p>
        @else
            <div class="relative overflow-x-auto border-y">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="py-3 pr-4 font-medium">Fee</th>
                            <th class="py-3 pr-4 text-right font-medium">Amount</th>
                            <th class="py-3 pr-4 text-right font-medium">Waiver</th>
                            <th class="py-3 pr-4 text-right font-medium">Fine</th>
                            <th class="py-3 pr-4 text-right font-medium">Paid</th>
                            <th class="py-3 text-right font-medium">Owed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($feeInvoice->feeInvoiceRecords as $record)
                            <tr wire:key="line-{{ $record->id }}">
                                <td class="py-3 pr-4 font-medium">{{ $record->fee?->name ?? $dash }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums">{{ $record->amount->formatToLocale($locale) }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums text-muted-foreground">{{ $record->waiver->isPositive() ? $record->waiver->formatToLocale($locale) : $dash }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums text-muted-foreground">{{ $record->fine->isPositive() ? $record->fine->formatToLocale($locale) : $dash }}</td>
                                <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums text-muted-foreground">{{ $record->paid->isPositive() ? $record->paid->formatToLocale($locale) : $dash }}</td>
                                <td @class(['whitespace-nowrap py-3 text-right tabular-nums', 'font-medium' => $record->outstanding->isPositive(), 'text-muted-foreground' => !$record->outstanding->isPositive()])>{{ $record->outstanding->formatToLocale($locale) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-medium">
                            <td class="py-3 pr-4">Total</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums">{{ $feeInvoice->amount->formatToLocale($locale) }}</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums">{{ $feeInvoice->waiver->formatToLocale($locale) }}</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums">{{ $feeInvoice->fine->formatToLocale($locale) }}</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right tabular-nums">{{ $feeInvoice->paid->formatToLocale($locale) }}</td>
                            <td class="whitespace-nowrap py-3 text-right tabular-nums">{{ $feeInvoice->balance->formatToLocale($locale) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>

    @if ($hasLines)
        <section aria-labelledby="payments-heading" class="flex flex-col gap-3">
            <h2 id="payments-heading" class="text-base font-semibold">Payments</h2>
            @if ($payments->isEmpty())
                <p class="text-sm text-muted-foreground">No payments</p>
            @else
                <ul class="divide-y border-y">
                    @foreach ($payments as $row)
                        <li wire:key="payment-{{ $row['payment']->id }}" class="flex items-center justify-between gap-4 py-2">
                            <div class="min-w-0 text-sm">
                                <p class="flex flex-wrap items-baseline gap-x-2">
                                    <span @class(['font-semibold tabular-nums', 'line-through text-muted-foreground' => $row['payment']->isReversed()])>{{ $row['amount']->formatToLocale($locale) }}</span>
                                    <span class="text-muted-foreground">{{ $row['payment']->methodLabel() }}</span>
                                    @if ($row['payment']->isReversed())
                                        <span class="text-muted-foreground">Taken back</span>
                                    @endif
                                </p>
                                <p class="text-muted-foreground">
                                    {{ $row['payment']->received_on?->format('j M Y') }}
                                    @if ($row['payment']->reference)
                                        · {{ $row['payment']->reference }}
                                    @endif
                                </p>
                            </div>
                            <april:button-link href="{{ route('student-payments.receipt', $row['payment']) }}" variant="ghost" class="h-11 shrink-0 select-none">Receipt</april:button-link>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
</div>
