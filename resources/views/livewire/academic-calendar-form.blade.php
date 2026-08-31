<april:card>
    <slot:title class="flex items-center gap-1">
        <span>{{ $academicYear ? 'Edit draft '.strtolower(school_term('academic_year', 'school year')) : 'Set up a '.strtolower(school_term('academic_year', 'school year')) }}</span>
        <x-help-tooltip label="Calendar setup help">Set the dates, choose a reporting structure, and review the generated periods before saving.</x-help-tooltip>
    </slot:title>
    <slot:description>{{ $academicYear ? 'Adjust the draft calendar.' : 'Set the dates and reporting periods.' }}</slot:description>
    <slot:content>
        @if (!$this->canEdit())
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 text-sm">
                This {{ strtolower(school_term('academic_year', 'school year')) }} is {{ strtolower($academicYear->statusLabel()) }}. Change individual periods from the calendar overview so its history stays clear.
            </div>
        @else
            <form wire:submit="save" class="space-y-8">
                @if ($showDateImpactWarning)
                    <div class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-4 text-sm" role="alert">
                        <p class="font-semibold">These date changes affect one-date timetables</p>
                        <p class="mt-1 text-muted-foreground">The timetable records will be kept, but these events will sit outside their reporting period until you move the event or adjust the dates.</p>
                        <ul class="mt-3 space-y-2">
                            @foreach ($dateImpactWarnings as $impact)
                                <li wire:key="date-impact-{{ $impact['id'] }}" class="flex flex-wrap justify-between gap-2 rounded-md border border-amber-500/20 bg-background px-3 py-2">
                                    <span class="font-medium">{{ $impact['timetable'] }} <span class="font-normal text-muted-foreground">in {{ $impact['period'] }}</span></span>
                                    <span class="text-muted-foreground">{{ $impact['date'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                            <button type="button" wire:click="reviewDateChanges" class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent">Review dates</button>
                            <button type="button" wire:click="saveWithDateImpact" class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-3 text-sm font-medium text-primary-foreground hover:bg-primary/90">Save dates and keep flagged events</button>
                        </div>
                    </div>
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="calendar-starts-on" class="text-sm font-medium">{{ school_term('academic_year', 'School year') }} starts on</label>
                        <input id="calendar-starts-on" type="date" wire:model="startsOn" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        @error('startsOn')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="calendar-ends-on" class="text-sm font-medium">{{ school_term('academic_year', 'School year') }} ends on</label>
                        <input id="calendar-ends-on" type="date" wire:model="endsOn" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        @error('endsOn')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>
                </div>

                <fieldset class="space-y-3">
                    <legend class="text-sm font-medium">Reporting structure</legend>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @foreach ($this->structures() as $value => $label)
                            <label wire:key="calendar-structure-{{ $value }}" class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 text-sm transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" wire:model.live="structure" value="{{ $value }}" class="size-4 border-input text-primary">
                                <span class="font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <section class="space-y-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="font-semibold">Reporting periods</h2>
                            <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                <span>Terms, semesters, or quarters used for reports.</span>
                                <x-help-tooltip label="Reporting periods help">These periods define the boundaries used by gradebooks and reports. Add breaks and events in the calendar after this is created.</x-help-tooltip>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="generatePeriods" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent">Generate periods</button>
                            <button type="button" wire:click="addPeriod" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent">Add period</button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($periods as $index => $period)
                            <div wire:key="calendar-period-{{ $index }}" class="grid gap-3 rounded-lg border p-4 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
                                <div class="flex flex-col gap-2">
                                    <label for="period-name-{{ $index }}" class="text-sm font-medium">Name</label>
                                    <input id="period-name-{{ $index }}" wire:model="periods.{{ $index }}.name" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                    @error('periods.'.$index.'.name')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="period-type-{{ $index }}" class="text-sm font-medium">Type</label>
                                    <select id="period-type-{{ $index }}" wire:model="periods.{{ $index }}.type" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                        @foreach ($this->periodTypes() as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="period-start-{{ $index }}" class="text-sm font-medium">Starts</label>
                                    <input id="period-start-{{ $index }}" type="date" wire:model="periods.{{ $index }}.starts_on" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="period-end-{{ $index }}" class="text-sm font-medium">Ends</label>
                                    <input id="period-end-{{ $index }}" type="date" wire:model="periods.{{ $index }}.ends_on" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                </div>
                                <button type="button" wire:click="removePeriod({{ $index }})" class="inline-flex h-10 items-center justify-center rounded-md border border-input px-3 text-sm font-medium text-destructive hover:bg-destructive/10" aria-label="Remove {{ $period['name'] }}">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    @error('periods')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <april:button-link href="{{ route('academic-years.index') }}" variant="outline">Cancel</april:button-link>
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $academicYear ? 'Save draft '.strtolower(school_term('academic_year', 'school year')) : 'Create draft '.strtolower(school_term('academic_year', 'school year')) }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </form>
        @endif
    </slot:content>
</april:card>
