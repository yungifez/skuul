<form wire:submit="save" class="flex flex-col gap-4" aria-label="{{ $dormitory === null ? 'Open a house' : 'Change '.$dormitory->name }}">
    @php($controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring')

    <div>
        <label for="house-name" class="text-sm text-muted-foreground">Name</label>
        <input id="house-name" wire:model="name" required maxlength="100" autocomplete="off" placeholder="e.g. Mandela House" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>

    <div>
        <label for="house-label" class="text-sm text-muted-foreground">What this school calls it</label>
        <input id="house-label" wire:model="label" required maxlength="40" autocomplete="off" aria-describedby="house-label-hint" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('label') }}>
        <p id="house-label-hint" class="mt-1 text-xs text-muted-foreground">House, hostel, block. The screens use your word.</p>
        <x-field-error name="label" class="mt-1" />
    </div>

    @if ($dormitory === null)
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="house-rooms" class="text-sm text-muted-foreground">Rooms</label>
                <input id="house-rooms" type="number" wire:model="rooms" min="1" max="200" required inputmode="numeric" class="{{ $controlClasses }} mt-1 tabular-nums" {{ field_error_bindings('rooms') }}>
                <x-field-error name="rooms" class="mt-1" />
            </div>
            <div>
                <label for="house-beds" class="text-sm text-muted-foreground">Beds in each room</label>
                <input id="house-beds" type="number" wire:model="bedsPerRoom" min="1" max="40" required inputmode="numeric" class="{{ $controlClasses }} mt-1 tabular-nums" {{ field_error_bindings('bedsPerRoom') }}>
                <x-field-error name="bedsPerRoom" class="mt-1" />
            </div>
        </div>
        <p class="text-xs text-muted-foreground">Rooms and beds are named for you. Rename any of them on the house page.</p>
    @endif

    <div>
        <label for="house-notes" class="text-sm text-muted-foreground">Notes (optional)</label>
        <textarea id="house-notes" wire:model="notes" rows="3" maxlength="1000" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('notes') }}></textarea>
        <x-field-error name="notes" class="mt-1" />
    </div>

    @if ($dormitory !== null)
        <div class="border-y py-3">
            <label class="flex min-h-11 cursor-pointer select-none items-center justify-between gap-3">
                <span>
                    <span class="block text-sm font-medium">Takes boarders</span>
                    <span class="block text-xs text-muted-foreground">
                        {{ $hasBoarders ? 'Move the boarders out before closing the house.' : 'A closed house keeps its rooms, beds and history.' }}
                    </span>
                </span>
                <input type="checkbox" role="switch" wire:model.live="isActive" @disabled($hasBoarders && $isActive)
                    @class([
                        'relative h-6 w-10 shrink-0 cursor-pointer appearance-none rounded-full transition-colors before:absolute before:left-0.5 before:top-0.5 before:size-5 before:rounded-full before:bg-background before:shadow before:transition-transform focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
                        'bg-foreground before:translate-x-4' => $isActive,
                        'bg-input' => !$isActive,
                    ])
                    {{ field_error_bindings('isActive') }}>
            </label>
            <x-field-error name="isActive" class="mt-1" />
        </div>
    @endif

    <div class="flex justify-end gap-2">
        <april:button-link href="{{ $dormitory === null ? route('dormitories.index') : route('dormitories.show', $dormitory) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $dormitory === null ? 'Open the house' : 'Save changes' }}</april:button>
    </div>
</form>
