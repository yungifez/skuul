<form wire:submit="save" class="flex flex-col gap-2 border-t pt-4 md:w-1/2">
    <label for="library-name" class="text-sm font-medium">Save to the curriculum library</label>
    <p class="text-sm text-muted-foreground">Teachers can then copy this weekly plan into their own drafts.</p>
    <input id="library-name" type="text" wire:model="name" {{ field_error_bindings('name') }}
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
    <x-field-error name="name" />
    <div><april:button type="submit" variant="outline">Save to library</april:button></div>
</form>
