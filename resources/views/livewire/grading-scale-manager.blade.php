<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $valueLabel = match ($scaleType) {
            'percentage' => 'Percentage',
            'gpa' => 'GPA',
            'points' => 'Points (optional)',
            default => null,
        };
        $basisHint = match ($scaleType) {
            'percentage' => 'Each option records a percentage. 85 means 85%.',
            'gpa' => 'Each option records a GPA. On a 4.0 scale, 3.0 becomes 75% in reports.',
            'points' => 'Each option records exact points. The assessment maximum turns them into a percentage.',
            'descriptive' => 'Words only, such as Excellent or Developing.',
            default => '',
        };
    @endphp

    @if (!$isEditing)
        <div>
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startCreating">
                <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Create a grading scale
            </april:button>
        </div>
    @else
        <form wire:submit="save" class="flex flex-col gap-4 border-y py-4" aria-label="{{ $editingId === null ? 'Create a grading scale' : 'Change '.$name }}">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="scale-name" class="text-sm text-muted-foreground">Name</label>
                    <input id="scale-name" wire:model="name" required maxlength="100" placeholder="Primary school grades" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                    <x-field-error name="name" class="mt-1" />
                </div>
                <div>
                    <label for="scale-type" class="text-sm text-muted-foreground">Basis</label>
                    <select id="scale-type" wire:model.live="scaleType" class="{{ $controlClasses }}" {{ field_error_bindings('scaleType') }}>
                        @foreach ($scaleTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="scaleType" class="mt-1" />
                    <p class="mt-1 text-xs text-muted-foreground">{{ $basisHint }}</p>
                </div>
                @if ($scaleType === 'gpa')
                    <div>
                        <label for="scale-maximum" class="text-sm text-muted-foreground">Maximum GPA</label>
                        <input id="scale-maximum" type="number" inputmode="decimal" min="0.01" step="0.01" wire:model="maximumValue" class="{{ $controlClasses }}" {{ field_error_bindings('maximumValue') }}>
                        <x-field-error name="maximumValue" class="mt-1" />
                    </div>
                @endif
                <div class="sm:col-span-2">
                    <label for="scale-description" class="text-sm text-muted-foreground">When to use it (optional)</label>
                    <input id="scale-description" wire:model="description" maxlength="5000" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
                    <x-field-error name="description" class="mt-1" />
                </div>
            </div>

            <fieldset class="flex flex-col gap-2">
                <legend class="text-sm text-muted-foreground">Grade options, best first</legend>
                <ul class="flex flex-col gap-2">
                    @foreach ($options as $index => $option)
                        @php($isRecorded = $option['id'] !== null && in_array($option['id'], $recordedOptionIds, true))
                        <li wire:key="option-{{ $option['id'] ?? 'new-'.$index }}" class="flex items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <label for="option-label-{{ $index }}" class="sr-only">Grade option {{ $index + 1 }}</label>
                                <input id="option-label-{{ $index }}" wire:model="options.{{ $index }}.label" maxlength="100" placeholder="Excellent" @readonly($isRecorded) @class([$controlClasses, 'mt-0', 'bg-muted' => $isRecorded]) {{ field_error_bindings('options.'.$index.'.label') }}>
                                <x-field-error :name="'options.'.$index.'.label'" class="mt-1" />
                            </div>
                            @if ($valueLabel !== null)
                                <div class="w-28 shrink-0">
                                    <label for="option-points-{{ $index }}" class="sr-only">{{ $valueLabel }} for grade option {{ $index + 1 }}</label>
                                    <input id="option-points-{{ $index }}" type="number" inputmode="decimal" min="0" step="0.01" wire:model="options.{{ $index }}.points" placeholder="{{ $valueLabel }}" @readonly($isRecorded) @class([$controlClasses, 'mt-0', 'bg-muted' => $isRecorded]) {{ field_error_bindings('options.'.$index.'.points') }}>
                                    <x-field-error :name="'options.'.$index.'.points'" class="mt-1" />
                                </div>
                            @endif
                            @if ($isRecorded)
                                <span class="flex size-11 shrink-0 items-center justify-center text-muted-foreground" title="Used in learner records, so it cannot change">
                                    <x-lucide-lock class="size-4" aria-hidden="true" /><span class="sr-only">Used in learner records, so it cannot change</span>
                                </span>
                            @else
                                <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" wire:click="removeOption({{ $index }})" aria-label="Remove grade option {{ $index + 1 }}">
                                    <x-lucide-x class="size-4" />
                                </april:button>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <x-field-error name="options" />
                <button type="button" wire:click="addOption" class="min-h-11 select-none self-start text-sm font-medium underline-offset-4 hover:underline">Add another option</button>
            </fieldset>

            <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                <input type="checkbox" wire:model="isActive" class="size-4 rounded border-input">
                Offer it for new assessments
            </label>

            <div class="flex justify-end gap-2">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopEditing">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $editingId === null ? 'Create the scale' : 'Save' }}</april:button>
            </div>
        </form>
    @endif

    <section class="flex flex-col gap-2" aria-labelledby="scales-heading">
        <h2 id="scales-heading" class="text-base font-semibold">The school's scales</h2>
        @if ($scales->isEmpty())
            <p class="text-sm text-muted-foreground">No scale yet. Teachers mark with numbers until one exists.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($scales as $scale)
                    <li wire:key="scale-{{ $scale->id }}" @class(['flex items-center gap-3 py-3', 'text-muted-foreground' => !$scale->is_active])>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $scale->name }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $scale->scale_type->label() }} · {{ $scale->options->map(fn ($option) => $option->points === null ? $option->label : $option->label.' '.rtrim(rtrim(number_format($option->points, 2), '0'), '.'))->join(', ') ?: '—' }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ $scale->grade_items_count }} {{ Str::plural('assessment', $scale->grade_items_count) }}{{ $scale->is_active ? '' : ' · not offered for new assessments' }}
                            </p>
                        </div>
                        <april:dropdown-menu>
                            <slot:trigger>
                                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $scale->name }}">
                                    <x-lucide-ellipsis class="size-4" />
                                </april:button>
                            </slot:trigger>
                            <slot:content align="end">
                                <april:dropdown-menu-item wire:click="startChanging({{ $scale->id }})"><x-lucide-pencil class="mr-2 size-4" />Change</april:dropdown-menu-item>
                                @if ($scale->is_active)
                                    <april:dropdown-menu-item wire:click="setOffered({{ $scale->id }}, false)"><x-lucide-eye-off class="mr-2 size-4" />Stop offering it</april:dropdown-menu-item>
                                @else
                                    <april:dropdown-menu-item wire:click="setOffered({{ $scale->id }}, true)"><x-lucide-eye class="mr-2 size-4" />Offer it again</april:dropdown-menu-item>
                                @endif
                                @if ($scale->grade_items_count === 0)
                                    <april:dropdown-menu-item wire:click="delete({{ $scale->id }})" wire:confirm="Delete {{ $scale->name }}? It cannot be brought back."><x-lucide-trash-2 class="mr-2 size-4" />Delete</april:dropdown-menu-item>
                                @endif
                            </slot:content>
                        </april:dropdown-menu>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
