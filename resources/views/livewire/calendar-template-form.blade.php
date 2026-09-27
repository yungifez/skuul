<form wire:submit="save" class="flex flex-col gap-6" aria-label="{{ $calendarTemplate === null ? 'Create a calendar template' : 'Change '.$calendarTemplate->name }}">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $switchClasses = 'mt-0.5 size-4 shrink-0 rounded border-input';
    @endphp

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="template-name" class="text-sm text-muted-foreground">Template name</label>
            <input id="template-name" type="text" wire:model="name" required maxlength="100" autocomplete="off" placeholder="Three-term calendar" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="template-cycle-length" class="text-sm text-muted-foreground">School year length in days</label>
            <input id="template-cycle-length" type="number" wire:model="cycleLengthDays" required min="1" max="3660" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('cycleLengthDays') }}>
            <x-field-error name="cycleLengthDays" class="mt-1" />
        </div>
        <div class="md:col-span-2">
            <label for="template-description" class="text-sm text-muted-foreground">Description (optional)</label>
            <textarea id="template-description" wire:model="description" rows="3" maxlength="500" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
            <x-field-error name="description" class="mt-1" />
        </div>
    </div>

    <fieldset class="flex flex-col gap-4">
        <legend class="mb-2 text-sm font-medium">Automation</legend>
        <ul class="divide-y border-y">
            <li>
                <label class="flex min-h-11 cursor-pointer select-none items-start gap-3 py-3 text-sm">
                    <input type="checkbox" wire:model="isDefault" class="{{ $switchClasses }}" {{ field_error_bindings('isDefault') }}>
                    <span><span class="font-medium">Organization default</span><span class="block text-muted-foreground">Campuses follow this template unless one deliberately chooses another.</span></span>
                </label>
            </li>
            <li>
                <label class="flex min-h-11 cursor-pointer select-none items-start gap-3 py-3 text-sm">
                    <input type="checkbox" wire:model="autoOpen" class="{{ $switchClasses }}" {{ field_error_bindings('autoOpen') }}>
                    <span><span class="font-medium">Open on the start date</span><span class="block text-muted-foreground">Scheduled school years and periods open by themselves. Closing stays manual.</span></span>
                </label>
            </li>
        </ul>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="template-generate-ahead" class="text-sm text-muted-foreground">Generate the next school year (weeks ahead)</label>
                <input id="template-generate-ahead" type="number" wire:model="generateAheadWeeks" required min="0" max="104" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('generateAheadWeeks') }}>
                <x-field-error name="generateAheadWeeks" class="mt-1" />
            </div>
            <div>
                <label for="template-remind" class="text-sm text-muted-foreground">Reminder lead time (days)</label>
                <input id="template-remind" type="number" wire:model="remindDaysBefore" required min="0" max="90" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('remindDaysBefore') }}>
                <x-field-error name="remindDaysBefore" class="mt-1" />
            </div>
        </div>
    </fieldset>

    <section class="flex flex-col gap-3" aria-labelledby="template-periods-heading">
        <div>
            <h2 id="template-periods-heading" class="font-semibold">School year periods</h2>
            <p class="text-sm text-muted-foreground">Days count from the first day of the school year, starting at 0. A sub-period sits inside an earlier row.</p>
        </div>

        <ol class="divide-y border-y">
            @foreach ($periods as $index => $period)
                @php($rowNumber = $index + 1)
                <li wire:key="template-period-{{ $index }}" class="grid gap-3 py-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-end gap-2 sm:col-span-2">
                        <div class="min-w-0 flex-1">
                            <label for="period-{{ $index }}-name" class="text-sm text-muted-foreground">Row {{ $rowNumber }} name</label>
                            <input id="period-{{ $index }}-name" type="text" wire:model="periods.{{ $index }}.name" required maxlength="100" autocomplete="off" placeholder="Term {{ $rowNumber }}" class="{{ $controlClasses }}" {{ field_error_bindings("periods.$index.name") }}>
                        </div>
                        @if (count($periods) > 1)
                            <april:button type="button" variant="ghost" class="size-11 shrink-0 select-none p-0" wire:click="removePeriod({{ $index }})" aria-label="Remove row {{ $rowNumber }}">
                                <x-lucide-x class="size-4" />
                            </april:button>
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <label for="period-{{ $index }}-label" class="text-sm text-muted-foreground">Local label (optional)</label>
                        <input id="period-{{ $index }}-label" type="text" wire:model="periods.{{ $index }}.label" maxlength="100" autocomplete="off" class="{{ $controlClasses }}" {{ field_error_bindings("periods.$index.label") }}>
                    </div>
                    <div>
                        <label for="period-{{ $index }}-type" class="text-sm text-muted-foreground">Type</label>
                        <select id="period-{{ $index }}-type" wire:model="periods.{{ $index }}.type" class="{{ $controlClasses }}" {{ field_error_bindings("periods.$index.type") }}>
                            @foreach ($periodTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="period-{{ $index }}-parent" class="text-sm text-muted-foreground">Inside</label>
                        <select id="period-{{ $index }}-parent" wire:model="periods.{{ $index }}.parent_index" class="{{ $controlClasses }}" {{ field_error_bindings("periods.$index.parent_index") }}>
                            <option value="">The whole year</option>
                            @for ($parent = 1; $parent < $rowNumber; $parent++)
                                <option value="{{ $parent }}">Row {{ $parent }}{{ filled($periods[$parent - 1]['name'] ?? null) ? ': '.$periods[$parent - 1]['name'] : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:col-span-2">
                        <div>
                            <label for="period-{{ $index }}-start" class="text-sm text-muted-foreground">Starts on day</label>
                            <input id="period-{{ $index }}-start" type="number" wire:model="periods.{{ $index }}.start_offset_days" required min="0" max="3660" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings("periods.$index.start_offset_days") }}>
                        </div>
                        <div>
                            <label for="period-{{ $index }}-length" class="text-sm text-muted-foreground">Days</label>
                            <input id="period-{{ $index }}-length" type="number" wire:model="periods.{{ $index }}.length_days" required min="1" max="3660" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings("periods.$index.length_days") }}>
                        </div>
                        <div>
                            <label for="period-{{ $index }}-position" class="text-sm text-muted-foreground">Order</label>
                            <input id="period-{{ $index }}-position" type="number" wire:model="periods.{{ $index }}.position" required min="1" max="99" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings("periods.$index.position") }}>
                        </div>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4">
                        @foreach (['name', 'label', 'type', 'parent_index', 'start_offset_days', 'length_days', 'position'] as $field)
                            <x-field-error name="periods.{{ $index }}.{{ $field }}" />
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ol>
        <x-field-error name="periods" />

        @if ($canAddPeriod)
            <div>
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="addPeriod" wire:loading.attr="disabled" wire:target="addPeriod">Add a period</april:button>
            </div>
        @endif
    </section>

    <div class="flex flex-wrap justify-end gap-2">
        <april:button-link href="{{ route('organizations.calendar-templates.index', $organization) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $calendarTemplate === null ? 'Create calendar template' : 'Save calendar template' }}</april:button>
    </div>
</form>
