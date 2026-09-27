<form wire:submit="save" class="flex max-w-3xl flex-col gap-6" aria-label="Write a role">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="name" class="text-sm text-muted-foreground">Name</label>
            <input id="name" wire:model="name" required maxlength="100" placeholder="Registrar" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="description" class="text-sm text-muted-foreground">What it is for (optional)</label>
            <input id="description" wire:model="description" maxlength="255" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
            <x-field-error name="description" class="mt-1" />
        </div>
    </div>

    <x-role-permissions :grantable="$grantable" />

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Write the role</april:button>
    </div>
</form>
