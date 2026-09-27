<form wire:submit="save" class="flex max-w-md flex-col gap-4" aria-label="Rename the timetable item">
    <div>
        <label for="name" class="text-sm text-muted-foreground">Name</label>
        <input id="name" wire:model="name" required maxlength="255" placeholder="Break" class="mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('name') }}>
        <p class="mt-1 text-xs text-muted-foreground">Something that fills a period but is not a lesson, such as a break or assembly.</p>
        <x-field-error name="name" class="mt-1" />
    </div>

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save the name</april:button>
    </div>
</form>
