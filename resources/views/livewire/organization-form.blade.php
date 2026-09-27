<form wire:submit="save" class="flex max-w-xl flex-col gap-4" aria-label="{{ $organization === null ? 'Create an organization' : 'Organization settings' }}">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    @if ($organization === null)
        <p class="text-sm text-muted-foreground">One organization holds a school group, a district, or a single school. Campuses are added afterwards.</p>
    @endif

    <div>
        <label for="name" class="text-sm text-muted-foreground">Name</label>
        <input id="name" wire:model="name" required maxlength="255" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>
    <div>
        <label for="code" class="text-sm text-muted-foreground">Code{{ $organization === null ? ' (optional)' : '' }}</label>
        <input id="code" wire:model="code" @required($organization !== null) maxlength="50" autocomplete="off" class="{{ $controlClasses }} font-mono uppercase" {{ field_error_bindings('code') }}>
        <p class="mt-1 text-xs text-muted-foreground">Letters, numbers, dashes and underscores.{{ $organization === null ? ' Leave it empty to get one made up.' : '' }}</p>
        <x-field-error name="code" class="mt-1" />
    </div>
    <div>
        <label for="address" class="text-sm text-muted-foreground">Address (optional)</label>
        <input id="address" wire:model="address" maxlength="1000" autocomplete="street-address" class="{{ $controlClasses }}" {{ field_error_bindings('address') }}>
        <x-field-error name="address" class="mt-1" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="email" class="text-sm text-muted-foreground">Email (optional)</label>
            <input id="email" type="email" wire:model="email" maxlength="255" autocomplete="email" class="{{ $controlClasses }}" {{ field_error_bindings('email') }}>
            <x-field-error name="email" class="mt-1" />
        </div>
        <div>
            <label for="phone" class="text-sm text-muted-foreground">Phone (optional)</label>
            <input id="phone" type="tel" wire:model="phone" maxlength="50" autocomplete="tel" class="{{ $controlClasses }}" {{ field_error_bindings('phone') }}>
            <x-field-error name="phone" class="mt-1" />
        </div>
    </div>
    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $organization === null ? 'Create the organization' : 'Save' }}</april:button>
    </div>
</form>
