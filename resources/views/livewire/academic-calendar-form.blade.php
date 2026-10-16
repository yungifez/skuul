<div class="min-w-0">
    @if (!$this->canEdit())
        @php
            $dateRange = static fn (?string $startsOn, ?string $endsOn): string => blank($startsOn) && blank($endsOn)
                ? 'No dates'
                : collect([$startsOn, $endsOn])->map(fn (?string $date): string => blank($date) ? '—' : \Illuminate\Support\Carbon::parse($date)->format('M j, Y'))->join(' – ');
        @endphp
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="flex items-center gap-2 text-sm text-muted-foreground">
                    <x-lucide-lock class="size-4 shrink-0" />
                    {{ $academicYear->statusLabel() }} {{ strtolower(school_terms('academic_year', 'school years')) }} change one period at a time.
                </p>
                <april:button-link href="{{ route('academic-years.show', $academicYear) }}" variant="outline">
                    <x-lucide-calendar class="mr-1.5 size-4" />Open calendar
                </april:button-link>
            </div>
            <dl class="divide-y border-y text-sm">
                <div class="grid min-h-11 grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-2">
                    <dt class="font-medium">{{ school_term('academic_year', 'School year') }}</dt>
                    <dd class="tabular-nums text-muted-foreground">{{ $dateRange($academicYear->starts_on?->toDateString(), $academicYear->ends_on?->toDateString()) }}</dd>
                </div>
                @foreach ($periods as $period)
                    <div wire:key="calendar-period-readonly-{{ $period['id'] ?? $loop->index }}" class="grid min-h-11 grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-2 pl-4">
                        <dt>{{ $period['name'] }}</dt>
                        <dd class="tabular-nums text-muted-foreground">{{ $dateRange($period['starts_on'], $period['ends_on']) }}</dd>
                    </div>
                @endforeach
            </dl>
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
                        <button type="button" wire:click="reviewDateChanges" class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent">Review dates</button>
                        <button type="button" wire:click="saveWithDateImpact" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-3 text-sm font-medium text-primary-foreground hover:bg-primary/90">Save dates and keep flagged events</button>
                    </div>
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="calendar-starts-on" class="text-sm font-medium">Starts on</label>
                    <input id="calendar-starts-on" type="date" wire:model="startsOn" class="h-10 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('startsOn') }}>
                    <x-field-error name="startsOn" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="calendar-ends-on" class="text-sm font-medium">Ends on</label>
                    <input id="calendar-ends-on" type="date" wire:model="endsOn" class="h-10 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('endsOn') }}>
                    <x-field-error name="endsOn" />
                </div>
            </div>

            <fieldset class="space-y-2">
                <legend class="text-sm font-medium">Reporting structure</legend>
                <div class="grid grid-cols-2 gap-1 rounded-lg bg-muted p-1 sm:grid-cols-5">
                    @foreach ($this->structures() as $value => $label)
                        <label wire:key="calendar-structure-{{ $value }}" class="flex min-h-10 cursor-pointer select-none items-center justify-center rounded-md px-3 text-center text-sm text-muted-foreground transition hover:text-foreground has-[:checked]:bg-background has-[:checked]:font-medium has-[:checked]:text-foreground has-[:checked]:shadow-sm has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-ring">
                            <input type="radio" wire:model.live="structure" value="{{ $value }}" class="sr-only">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <section class="space-y-2" aria-labelledby="calendar-periods-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="calendar-periods-heading" class="text-sm font-medium">Reporting periods</h2>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" wire:click="generatePeriods" class="inline-flex h-10 select-none items-center gap-1.5 rounded-md px-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <x-lucide-rows-3 class="size-4" />Split evenly
                        </button>
                        <button type="button" wire:click="addPeriod" class="inline-flex h-10 select-none items-center gap-1.5 rounded-md px-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <x-lucide-plus class="size-4" />Add period
                        </button>
                    </div>
                </div>

                <div class="divide-y border-y">
                    <div class="hidden gap-3 py-2 text-xs font-medium text-muted-foreground md:grid md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_2.5rem]" aria-hidden="true">
                        <span>Name</span><span>Type</span><span>Starts</span><span>Ends</span><span></span>
                    </div>
                    @foreach ($periods as $index => $period)
                        <div wire:key="calendar-period-{{ $index }}" class="grid grid-cols-2 gap-3 py-3 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_2.5rem] md:items-start md:py-2">
                            <div class="col-span-2 flex flex-col gap-1 md:col-span-1">
                                <label for="period-name-{{ $index }}" class="text-xs font-medium text-muted-foreground md:sr-only">Name</label>
                                <input id="period-name-{{ $index }}" wire:model="periods.{{ $index }}.name" class="h-10 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('periods.'.$index.'.name') }}>
                                <x-field-error :name="'periods.'.$index.'.name'" />
                            </div>
                            <div class="col-span-2 flex flex-col gap-1 md:col-span-1">
                                <label for="period-type-{{ $index }}" class="text-xs font-medium text-muted-foreground md:sr-only">Type</label>
                                <select id="period-type-{{ $index }}" wire:model="periods.{{ $index }}.type" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach ($this->periodTypes() as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex min-w-0 flex-col gap-1">
                                <label for="period-start-{{ $index }}" class="text-xs font-medium text-muted-foreground md:sr-only">Starts</label>
                                <input id="period-start-{{ $index }}" type="date" wire:model="periods.{{ $index }}.starts_on" class="h-10 min-w-0 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('periods.'.$index.'.starts_on') }}>
                                <x-field-error :name="'periods.'.$index.'.starts_on'" />
                            </div>
                            <div class="flex min-w-0 flex-col gap-1">
                                <label for="period-end-{{ $index }}" class="text-xs font-medium text-muted-foreground md:sr-only">Ends</label>
                                <input id="period-end-{{ $index }}" type="date" wire:model="periods.{{ $index }}.ends_on" class="h-10 min-w-0 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('periods.'.$index.'.ends_on') }}>
                                <x-field-error :name="'periods.'.$index.'.ends_on'" />
                            </div>
                            <div class="col-span-2 flex justify-end md:col-span-1">
                                @if (count($periods) > 1)
                                    <button type="button" wire:click="removePeriod({{ $index }})" class="inline-flex size-10 items-center justify-center rounded-md text-muted-foreground hover:bg-destructive/10 hover:text-destructive" aria-label="Remove {{ $period['name'] }}" title="Remove {{ $period['name'] }}">
                                        <x-lucide-trash-2 class="size-4" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-field-error name="periods" />
            </section>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                @unless ($setupWizard && $academicYear)
                    <april:button-link href="{{ route('academic-years.index') }}" variant="ghost">Cancel</april:button-link>
                @endunless
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ $academicYear ? ($setupWizard ? 'Save and continue' : 'Save draft') : 'Create draft '.strtolower(school_term('academic_year', 'school year')) }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
            </div>
        </form>
    @endif
</div>
