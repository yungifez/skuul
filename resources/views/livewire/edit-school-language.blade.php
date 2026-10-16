<div class="mx-auto flex w-full max-w-3xl flex-col gap-10">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <form wire:submit="save" class="flex flex-col gap-10" aria-label="School language">
        <fieldset class="flex flex-col gap-2">
            <legend class="mb-2 text-base font-semibold">Starting language pattern</legend>
            <ul class="divide-y border-y">
                @foreach ($presetOptions as $value => $option)
                    <li wire:key="preset-{{ $value }}">
                        <label class="flex min-h-11 cursor-pointer items-start gap-3 py-3 select-none">
                            <input type="radio" wire:model.live="preset" value="{{ $value }}" class="mt-0.5 size-4 shrink-0">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium">
                                    {{ $option['title'] }}
                                    @if ($value === \App\Models\SchoolOperatingProfile::DEFAULT_PRESET)
                                        <span class="font-normal text-muted-foreground">· Default</span>
                                    @endif
                                </span>
                                <span class="block text-sm text-muted-foreground">{{ implode(' · ', $option['labels']) }}</span>
                            </span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <x-field-error name="preset" />
        </fieldset>

        <fieldset class="flex flex-col gap-4">
            <legend class="mb-2 text-base font-semibold">Words your school uses</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($words as $key => $word)
                    <div wire:key="word-{{ $key }}">
                        <label for="label-{{ $key }}" class="text-sm text-muted-foreground">{{ $word }}</label>
                        <input id="label-{{ $key }}" wire:model="labels.{{ $key }}" maxlength="40" autocomplete="off" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('labels.'.$key) }}>
                        <x-field-error name="labels.{{ $key }}" class="mt-1" />
                    </div>
                @endforeach
            </div>
        </fieldset>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            @if ($setup)
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save and continue to classes</april:button>
            @else
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save">Save and continue to classes</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save</april:button>
            @endif
        </div>
    </form>
</div>
