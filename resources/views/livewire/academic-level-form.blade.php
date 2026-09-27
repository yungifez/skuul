<form wire:submit="save" class="flex flex-col gap-5" aria-label="{{ $academicLevel === null ? 'Add a level' : 'Change '.$academicLevel->name }}">
    @php($controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring')

    <div>
        <label for="level-name" class="inline-flex items-center gap-1 text-sm text-muted-foreground">
            Level name
            <x-help-tooltip label="Level name help">The level name is what staff read in lists, such as “Primary 4”. The school-wide label is the word this school uses for a level, such as “Class” or “Form”. It changes wording only and is set once in school setup.</x-help-tooltip>
        </label>
        <input id="level-name" type="text" wire:model="name" required maxlength="255" autocomplete="off" placeholder="Kindergarten, KG 1, or Primary 4" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>

    <div>
        <label class="flex min-h-11 cursor-pointer select-none items-start gap-3 border-y py-3">
            <input type="checkbox" wire:model.live="isGroup" class="mt-0.5 size-4 shrink-0 rounded border-input" {{ field_error_bindings('isGroup') }}>
            <span class="space-y-1 text-sm">
                <span class="flex items-center gap-1 font-medium">
                    This is a level group
                    <x-help-tooltip label="Level group help">Use this for an umbrella such as “Kindergarten”. It can contain classes such as “KG 1” and “KG 2”, and a subject can be taught to all of them as one group.</x-help-tooltip>
                </span>
                <span class="block text-muted-foreground">Groups do not have their own sections, but they can receive subjects taught across their child classes.</span>
            </span>
        </label>
        <x-field-error name="isGroup" class="mt-1" />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="level-code" class="text-sm text-muted-foreground">Short code (optional)</label>
            <input id="level-code" type="text" wire:model="code" maxlength="100" autocomplete="off" placeholder="P4" class="{{ $controlClasses }}" {{ field_error_bindings('code') }}>
            <x-field-error name="code" class="mt-1" />
        </div>
        <div>
            <label for="level-position" class="text-sm text-muted-foreground">Display order</label>
            <input id="level-position" type="number" wire:model="position" required min="0" max="9999" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('position') }}>
            <x-field-error name="position" class="mt-1" />
        </div>
        @unless ($isGroup)
            <div class="md:col-span-2">
                <label for="level-parent" class="text-sm text-muted-foreground">Level group (optional)</label>
                <select id="level-parent" wire:model="parentId" aria-describedby="level-parent-hint" class="{{ $controlClasses }}" {{ field_error_bindings('parentId') }}>
                    <option value="">No group. This is a standalone level.</option>
                    @foreach ($parentOptions as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>
                <p id="level-parent-hint" class="mt-1 text-xs text-muted-foreground">Choose a group when this level belongs under an umbrella such as “Kindergarten”.</p>
                <x-field-error name="parentId" class="mt-1" />
            </div>
        @endunless
    </div>

    <div class="flex flex-wrap justify-end gap-2">
        <april:button-link href="{{ $academicLevel === null ? route('academic-levels.index') : route('academic-levels.show', $academicLevel) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $academicLevel === null ? 'Create class' : 'Save changes' }}</april:button>
    </div>
</form>
