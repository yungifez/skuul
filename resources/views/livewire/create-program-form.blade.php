<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Open a programme">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="name" class="text-sm text-muted-foreground">Name</label>
            <input id="name" wire:model="name" required maxlength="100" placeholder="Chess club" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="type" class="text-sm text-muted-foreground">Kind</label>
            <select id="type" wire:model="type" required class="{{ $controlClasses }}" {{ field_error_bindings('type') }}>
                @foreach ($types as $kind)
                    <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                @endforeach
            </select>
            <x-field-error name="type" class="mt-1" />
        </div>
        <div class="sm:col-span-2">
            <label for="description" class="text-sm text-muted-foreground">What it is (optional)</label>
            <textarea id="description" wire:model="description" rows="3" maxlength="1000" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
            <x-field-error name="description" class="mt-1" />
        </div>
    </div>

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Open the programme</april:button>
    </div>
</form>
