<april:card>
    <slot:title class="flex items-center gap-3 text-base">
        <span class="flex size-9 items-center justify-center rounded-md bg-muted">
            <x-icon name="lucide-map-pin" class="size-4" />
        </span>
        Working school
    </slot:title>
    <slot:description>Choose the school context for your next action.</slot:description>
    <slot:content>
        <form wire:submit="setSchool" class="space-y-5">
            <x-display-validation-errors />
            <div class="flex w-full flex-col gap-2">
                <april:label for="set-school-form">School branch</april:label>
                <april:select wire:model="school_id" id="set-school-form">
                @foreach ($schools as $school)
                <option value="{{ $school->id }}">{{ $school->name }} - {{ $school->address }}</option>
                @endforeach

                </april:select>
                <x-field-error name="school_id" />
            </div>
            <div class="flex justify-end">
                <april:button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto">
                    <span wire:loading.remove>Set working school</span>
                    <span wire:loading>Changing school…</span>
                </april:button>
            </div>
        </form>
    </slot:content>
</april:card>
