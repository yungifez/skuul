<form wire:submit="save" class="flex flex-col gap-6" aria-label="How this campus lends">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $fields = [
            ['loanDays', 'Days a loan lasts', 1, 365, null],
            ['renewalsAllowed', 'Renewals allowed', 0, 10, 'Zero means a book comes back before it goes out again.'],
            ['holdDays', 'Days to collect a hold', 1, 30, 'After this many days, the copy goes to the next person.'],
            ['learnerLimit', 'Items a learner may hold', 1, 100, null],
            ['staffLimit', 'Items a member of staff may hold', 1, 200, null],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ($fields as [$name, $label, $min, $max, $hint])
            <div wire:key="rule-{{ $name }}">
                <label for="rules-{{ $name }}" class="text-sm text-muted-foreground">{{ $label }}</label>
                <input id="rules-{{ $name }}" type="number" inputmode="numeric" min="{{ $min }}" max="{{ $max }}" required wire:model="{{ $name }}" class="{{ $controlClasses }}" {{ field_error_bindings($name) }}>
                <x-field-error :name="$name" class="mt-1" />
                @if ($hint)
                    <p class="mt-1 text-xs text-muted-foreground">{{ $hint }}</p>
                @endif
            </div>
        @endforeach

        <div class="sm:col-span-2">
            <label for="rules-finePerDay" class="text-sm text-muted-foreground">What one late day costs</label>
            <input id="rules-finePerDay" type="number" inputmode="decimal" step="0.01" min="0" required wire:model="finePerDay" class="{{ $controlClasses }}" {{ field_error_bindings('finePerDay') }}>
            <x-field-error name="finePerDay" class="mt-1" />
            <p class="mt-1 text-xs text-muted-foreground">Zero means no fines. A fine goes on the learner's fee account, so one balance shows what a family owes.</p>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t pt-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('library-copies.index') }}" class="inline-flex h-11 select-none items-center justify-center rounded-md px-4 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground">Back to the library</a>
        <april:button type="submit" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="save">Save the rules</april:button>
    </div>
</form>
