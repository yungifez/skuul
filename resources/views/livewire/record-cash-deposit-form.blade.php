<div class="mx-auto flex w-full max-w-xl flex-col gap-6">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <dl class="grid grid-cols-2 gap-4 border-y py-4">
        <div>
            <dt class="text-sm text-muted-foreground">In the cash box</dt>
            <dd class="text-lg font-semibold tabular-nums">{{ money_text($cashBox) }}</dd>
        </div>
        <div>
            <dt class="text-sm text-muted-foreground">Left after this deposit</dt>
            <dd class="text-lg font-semibold tabular-nums">{{ is_numeric($amount) ? money_text($cashBox - (float) $amount) : '—' }}</dd>
        </div>
    </dl>

    <form wire:submit="save" class="flex flex-col gap-4" aria-label="Record cash deposit">
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="deposit-amount" class="text-sm text-muted-foreground">Amount</label>
                <input type="number" id="deposit-amount" wire:model.live.debounce.400ms="amount" step="0.01" min="0.01" required inputmode="decimal" placeholder="0.00" class="{{ $controlClasses }} mt-1 text-right tabular-nums" {{ field_error_bindings('amount') }}>
            </div>
            <div>
                <label for="deposit-date" class="text-sm text-muted-foreground">Date</label>
                <input type="date" id="deposit-date" wire:model="depositDate" max="{{ now()->toDateString() }}" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('depositDate') }}>
            </div>
        </div>
        <x-field-error name="amount" />
        <x-field-error name="depositDate" />

        @if ($isAboveCashBox)
            <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                <input type="checkbox" wire:model="confirmsMoreThanCashBox" class="size-4 rounded border-input">
                The cash was counted and this amount is right
            </label>
        @endif

        <div>
            <label for="deposit-reference" class="sr-only">Bank reference (optional)</label>
            <input id="deposit-reference" wire:model="bankReference" maxlength="100" placeholder="Bank reference (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('bankReference') }}>
            <x-field-error name="bankReference" class="mt-1" />
        </div>
        <div>
            <label for="deposit-note" class="sr-only">Note (optional)</label>
            <textarea id="deposit-note" wire:model="note" rows="3" maxlength="2000" placeholder="Note (optional)"
                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('note') }}></textarea>
            <x-field-error name="note" />
        </div>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ route('cash-deposits.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Record deposit</april:button>
        </div>
    </form>
</div>
