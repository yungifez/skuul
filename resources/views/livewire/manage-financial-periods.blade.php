<div class="flex flex-col gap-3">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="flex items-center justify-between gap-4">
        <h2 class="text-base font-semibold">Financial periods</h2>
        @if ($canManage && !$isAdding)
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="$set('isAdding', true)">Add period</april:button>
        @endif
    </div>

    @if ($canManage && $isAdding)
        <form wire:submit="save" class="flex flex-col gap-3" aria-label="Add a financial period">
            <div class="grid gap-3 sm:grid-cols-[1fr_11rem_11rem]">
                <div>
                    <label for="period-name" class="sr-only">Name</label>
                    <input id="period-name" wire:model="name" maxlength="100" placeholder="2027 financial year" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                </div>
                <div>
                    <label for="period-starts-on" class="sr-only">Starts on</label>
                    <input type="date" id="period-starts-on" wire:model="startsOn" class="{{ $controlClasses }}" {{ field_error_bindings('startsOn') }}>
                </div>
                <div>
                    <label for="period-ends-on" class="sr-only">Ends on</label>
                    <input type="date" id="period-ends-on" wire:model="endsOn" class="{{ $controlClasses }}" {{ field_error_bindings('endsOn') }}>
                </div>
            </div>
            <x-field-error name="name" />
            <x-field-error name="startsOn" />
            <x-field-error name="endsOn" />
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Add period</april:button>
            </div>
        </form>
    @endif

    <x-field-error name="period" />

    @if ($periods->isEmpty())
        <p class="text-sm text-muted-foreground">No financial periods</p>
    @else
        <ul class="divide-y border-y">
            @foreach ($periods as $period)
                <li wire:key="period-{{ $period->id }}" class="flex items-center justify-between gap-4 py-2">
                    <div class="min-w-0 text-sm">
                        <p class="flex flex-wrap items-baseline gap-x-2">
                            <span class="font-medium">{{ $period->name }}</span>
                            @if ($period->isClosed())
                                <span class="text-muted-foreground">Closed</span>
                            @endif
                        </p>
                        <p class="text-muted-foreground">{{ $period->starts_on->format('j M Y') }} – {{ $period->ends_on->format('j M Y') }}</p>
                    </div>
                    @if ($canManage)
                        @if ($period->isClosed())
                            <april:button type="button" variant="ghost" class="h-11 shrink-0 select-none" wire:click="reopen({{ $period->id }})" wire:confirm="Reopen {{ $period->name }}? Entries can be posted to it again.">Reopen</april:button>
                        @else
                            <april:button type="button" variant="ghost" class="h-11 shrink-0 select-none" wire:click="close({{ $period->id }})" wire:confirm="Close {{ $period->name }}? Nothing more can be posted to it.">Close</april:button>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
