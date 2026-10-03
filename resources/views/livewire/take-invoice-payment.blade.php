<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $locale = app()->getLocale();
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-3">
        <div class="col-span-2 sm:col-span-1">
            <dt class="text-sm text-muted-foreground">From</dt>
            <dd class="font-medium">{{ $feeInvoice->user?->name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-muted-foreground">Paid</dt>
            <dd class="font-medium tabular-nums">{{ $feeInvoice->paid->formatToLocale($locale) }}</dd>
        </div>
        <div class="col-span-2 sm:col-span-1">
            <dt class="text-sm text-muted-foreground">Owed</dt>
            <dd @class(['text-2xl font-semibold tabular-nums tracking-tight', 'text-muted-foreground' => !$feeInvoice->balance->isPositive()]) id="payment-owed">{{ $feeInvoice->balance->formatToLocale($locale) }}</dd>
        </div>
    </dl>

    @if ($openLines->isEmpty())
        <div class="flex flex-col items-start gap-3">
            <p class="text-sm">Paid in full.</p>
            @if ($feeInvoice->studentRecord !== null)
                <april:button-link href="{{ route('student-accounts.show', $feeInvoice->studentRecord) }}" variant="outline" class="h-11 select-none">Student account</april:button-link>
            @endif
        </div>
    @else
        <form wire:submit="save" class="flex flex-col gap-6" aria-label="Take a payment">
            <div class="grid gap-3 sm:grid-cols-[1fr_12rem]">
                <div>
                    <label for="payment-amount" class="mb-1.5 block text-sm font-medium">Amount</label>
                    <input id="payment-amount" type="number" step="0.01" min="0.01" inputmode="decimal" autocomplete="off" autofocus
                        wire:model="amount" placeholder="0.00" class="{{ $controlClasses }} text-base font-medium" {{ field_error_bindings('amount') }}>
                    <x-field-error name="amount" class="mt-1" />
                </div>
                <div>
                    <label for="payment-received-on" class="mb-1.5 block text-sm font-medium">Received on</label>
                    <input id="payment-received-on" type="date" max="{{ school_today()->toDateString() }}" wire:model="receivedOn" class="{{ $controlClasses }}" {{ field_error_bindings('receivedOn') }}>
                    <x-field-error name="receivedOn" class="mt-1" />
                </div>
            </div>

            <fieldset>
                <legend class="mb-1.5 text-sm font-medium">Paid by</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach ($channels as $key => $channel)
                        <label wire:key="channel-{{ $key }}" class="flex min-h-11 cursor-pointer items-center gap-2 rounded-md border px-3 text-sm select-none has-[:checked]:border-foreground has-[:checked]:font-medium">
                            <input type="radio" value="{{ $key }}" wire:model="method" class="size-4">
                            {{ $channel->label() }}
                        </label>
                    @endforeach
                </div>
                <x-field-error name="method" class="mt-1" />
            </fieldset>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="payment-reference" class="mb-1.5 block text-sm font-medium">Reference</label>
                    <input id="payment-reference" maxlength="100" autocomplete="off" wire:model="reference" placeholder="Optional" x-data="{ needs: @js(collect($channels)->map(fn ($channel): bool => $channel->needsReference())) }" x-bind:placeholder="needs[$wire.method] ? 'Required for this method' : 'Optional'" class="{{ $controlClasses }}" {{ field_error_bindings('reference') }}>
                    <x-field-error name="reference" class="mt-1" />
                </div>
                <div>
                    <label for="payment-note" class="mb-1.5 block text-sm font-medium">Note</label>
                    <input id="payment-note" maxlength="1000" autocomplete="off" wire:model="note" placeholder="Optional" class="{{ $controlClasses }}" {{ field_error_bindings('note') }}>
                    <x-field-error name="note" class="mt-1" />
                </div>
            </div>

            @if ($openLines->count() > 1)
                <div class="flex flex-col gap-3">
                    <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                        <input type="checkbox" wire:model.live="splitByFee" class="size-4 rounded border-input">
                        Split across fees
                    </label>

                    @if ($splitByFee)
                        <ul class="divide-y border-y" aria-label="Amount against each fee">
                            @foreach ($openLines as $line)
                                <li wire:key="line-{{ $line->id }}" class="flex items-center justify-between gap-4 py-2">
                                    <div class="min-w-0 text-sm">
                                        <p class="truncate font-medium">{{ $line->fee?->name ?? '—' }}</p>
                                        <p class="tabular-nums text-muted-foreground">{{ $line->outstanding->formatToLocale($locale) }} owed</p>
                                    </div>
                                    <div class="w-36 shrink-0">
                                        <label for="line-{{ $line->id }}" class="sr-only">Amount against {{ $line->fee?->name }}</label>
                                        <input id="line-{{ $line->id }}" type="number" step="0.01" min="0" max="{{ $line->outstanding->getAmount()->toFloat() }}" inputmode="decimal"
                                            wire:model="lines.{{ $line->id }}" placeholder="0.00" class="{{ $controlClasses }} text-right tabular-nums" {{ field_error_bindings('lines.'.$line->id) }}>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <x-field-error name="lines" />
                        @foreach ($openLines as $line)
                            <x-field-error name="lines.{{ $line->id }}" />
                        @endforeach
                    @endif
                </div>
            @endif

            <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                <april:button-link href="{{ route('fee-invoices.show', $feeInvoice) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Record payment</april:button>
            </div>
        </form>
    @endif
</div>
