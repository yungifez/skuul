<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <dl class="grid grid-cols-2 gap-4 border-y py-4">
        <div>
            <dt class="text-sm text-muted-foreground">In {{ strtolower($source?->name ?? 'the account') }}</dt>
            <dd class="text-lg font-semibold tabular-nums">{{ $balance === null ? '—' : money_text($balance) }}</dd>
        </div>
        <div>
            <dt class="text-sm text-muted-foreground">Left after this expense</dt>
            <dd class="text-lg font-semibold tabular-nums">{{ $balance !== null && is_numeric($amount) ? money_text($balance - (float) $amount) : '—' }}</dd>
        </div>
    </dl>

    @if ($accounts->isEmpty())
        <p class="text-sm text-muted-foreground">No expense accounts</p>
    @else
        <form wire:submit="save" class="flex flex-col gap-4" aria-label="Record expense">
            <div>
                <label for="expense-description" class="sr-only">What was bought</label>
                <input id="expense-description" wire:model="description" maxlength="255" required placeholder="What was bought, e.g. teaching supplies" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
                <x-field-error name="description" class="mt-1" />
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="expense-amount" class="text-sm text-muted-foreground">Amount</label>
                    <input type="number" id="expense-amount" wire:model.live.debounce.400ms="amount" step="0.01" min="0.01" required inputmode="decimal" placeholder="0.00" class="{{ $controlClasses }} mt-1 text-right tabular-nums" {{ field_error_bindings('amount') }}>
                </div>
                <div>
                    <label for="expense-date" class="text-sm text-muted-foreground">Date</label>
                    <input type="date" id="expense-date" wire:model="expenseDate" max="{{ now()->toDateString() }}" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('expenseDate') }}>
                </div>
                <div>
                    <label for="expense-account" class="text-sm text-muted-foreground">Spent on</label>
                    <select id="expense-account" wire:model="ledgerAccountId" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('ledgerAccountId') }}>
                        <option value="">Choose an account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="expense-method" class="text-sm text-muted-foreground">Paid from</label>
                    <select id="expense-method" wire:model.live="method" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('method') }}>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->key() }}">{{ $channel->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <x-field-error name="amount" />
            <x-field-error name="expenseDate" />
            <x-field-error name="ledgerAccountId" />
            <x-field-error name="method" />

            @if ($isAboveBalance)
                <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                    <input type="checkbox" wire:model="confirmsMoreThanBalance" class="size-4 rounded border-input">
                    This amount is right
                </label>
            @endif

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="expense-reference" class="sr-only">Reference{{ $needsReference ? '' : ' (optional)' }}</label>
                    <input id="expense-reference" wire:model="reference" maxlength="100" @required($needsReference) placeholder="{{ $needsReference ? 'Reference from the statement or slip' : 'Receipt number (optional)' }}" class="{{ $controlClasses }}" {{ field_error_bindings('reference') }}>
                    <x-field-error name="reference" class="mt-1" />
                </div>
                <div>
                    <label for="expense-vendor" class="sr-only">Who was paid (optional)</label>
                    <input id="expense-vendor" wire:model="vendor" maxlength="150" placeholder="Who was paid (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('vendor') }}>
                    <x-field-error name="vendor" class="mt-1" />
                </div>
                <div>
                    <label for="expense-program" class="sr-only">Programme (optional)</label>
                    <select id="expense-program" wire:model="programId" class="{{ $controlClasses }}" {{ field_error_bindings('programId') }}>
                        <option value="">All programmes</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="programId" class="mt-1" />
                </div>
                <div>
                    <label for="expense-fund" class="sr-only">Fund or grant (optional)</label>
                    <input id="expense-fund" wire:model="fund" maxlength="60" placeholder="Fund or grant (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('fund') }}>
                    <x-field-error name="fund" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="expense-note" class="sr-only">Note (optional)</label>
                <textarea id="expense-note" wire:model="note" rows="3" maxlength="2000" placeholder="Note (optional)"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('note') }}></textarea>
                <x-field-error name="note" />
            </div>

            <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                <april:button-link href="{{ route('expenses.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Record expense</april:button>
            </div>
        </form>
    @endif
</div>
