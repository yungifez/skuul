<div class="space-y-6">
    @error('timetable')
        <april:alert variant="destructive">
            <slot:title>That change was refused</slot:title>
            <slot:description>{{ $message }}</slot:description>
        </april:alert>
    @enderror

    @if ($this->outOfPeriodSlots->isNotEmpty())
        <april:alert variant="destructive">
            <slot:title>{{ $this->outOfPeriodSlots->count() }} one-date {{ Str::plural('slot', $this->outOfPeriodSlots->count()) }} need attention</slot:title>
            <slot:description>These dates are outside {{ $timetable->academicPeriod?->displayName ?? 'the academic period' }} after its dates changed. Correct the period or create a revision with dates inside it before publishing.</slot:description>
        </april:alert>
    @endif

    @if ($conflicts !== [])
        <april:alert variant="destructive">
            <slot:icon><x-lucide-triangle-alert class="size-4" /></slot:icon>
            <slot:title>{{ count($conflicts) }} {{ Str::plural('clash', count($conflicts)) }} to settle before publishing</slot:title>
            <slot:description>
                <ul class="mt-1 list-disc space-y-1 pl-4">
                    @foreach ($conflicts as $conflict)
                        <li>{{ $conflict }}</li>
                    @endforeach
                </ul>
            </slot:description>
        </april:alert>
    @endif

    <april:card>
        <slot:title>Calendar</slot:title>
        <slot:description>
            {{ $timetable->academicPeriod?->displayName ?? 'Academic period' }} ·
            recurring events follow the term dates automatically. Click a calendar date to inspect that day.
        </slot:description>
        <slot:content>
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border bg-muted/20 p-3">
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="goToCalendarToday" class="inline-flex h-9 items-center rounded-md border bg-background px-3 text-sm font-medium hover:bg-muted">Today</button>
                        <button type="button" wire:click="moveCalendar(-1)" class="inline-flex size-9 items-center justify-center rounded-md border bg-background hover:bg-muted" aria-label="Previous period">‹</button>
                        <button type="button" wire:click="moveCalendar(1)" class="inline-flex size-9 items-center justify-center rounded-md border bg-background hover:bg-muted" aria-label="Next period">›</button>
                        <span class="ml-1 text-sm font-semibold">{{ $this->calendarHeading() }}</span>
                    </div>
                    <div class="flex rounded-md border bg-background p-1">
                        @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month'] as $view => $label)
                            <button type="button" wire:click="setCalendarView('{{ $view }}')" class="rounded px-3 py-1.5 text-sm {{ $calendarView === $view ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                @if ($calendarView === 'week')
                    @include('livewire.partials.timetable-grid', [
                        'grid' => $grid,
                        'editable' => true,
                        'selected' => $selected,
                    ])
                @elseif ($calendarView === 'day')
                    <div class="space-y-2">
                        @forelse ($this->dayEvents as $event)
                            <button wire:key="day-event-{{ $event['key'] }}" type="button" wire:click="selectCell({{ explode(':', $event['key'])[0] }}, {{ explode(':', $event['key'])[1] }})" class="flex w-full items-center justify-between gap-3 rounded-md border bg-primary/10 px-4 py-3 text-left hover:border-primary">
                                <span><span class="font-medium">{{ $event['name'] }}</span>@if ($event['audience_role'])<span class="ml-2 text-xs text-muted-foreground">{{ ucfirst($event['audience_role']) }} only</span>@endif</span>
                                <span class="text-sm text-muted-foreground">{{ $event['time'] }}</span>
                            </button>
                        @empty
                            <button type="button" wire:click="setCalendarView('week')" class="w-full rounded-md border border-dashed p-10 text-center text-sm text-muted-foreground hover:border-primary">No events on this date. Choose a time slot from the week view to add one.</button>
                        @endforelse
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <div class="grid min-w-[48rem] grid-cols-7 gap-px overflow-hidden rounded-md border bg-border">
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $dayName)
                                <div class="bg-muted/60 px-2 py-2 text-center text-xs font-semibold">{{ $dayName }}</div>
                            @endforeach
                            @foreach ($this->monthWeeks as $week)
                                @foreach ($week as $day)
                                    <div wire:key="month-day-{{ $day['date'] }}" class="min-h-28 bg-background p-2 {{ !$day['in_month'] ? 'bg-muted/20 text-muted-foreground' : '' }} {{ !$day['in_period'] && $day['in_month'] ? 'bg-amber-50/50 dark:bg-amber-950/10' : '' }}">
                                        <button type="button" wire:click="chooseCalendarDate('{{ $day['date'] }}')" class="mb-2 flex size-7 items-center justify-center rounded-full text-xs font-semibold hover:bg-primary hover:text-primary-foreground {{ $day['date'] === $calendarDate ? 'bg-primary text-primary-foreground' : '' }}">{{ $day['day'] }}</button>
                                        <div class="space-y-1">
                                            @foreach ($day['events'] as $event)
                                                <button wire:key="month-event-{{ $day['date'] }}-{{ $event['key'] }}" type="button" wire:click="selectCell({{ explode(':', $event['key'])[0] }}, {{ explode(':', $event['key'])[1] }})" class="block w-full truncate rounded border border-primary/20 bg-primary/10 px-1.5 py-1 text-left text-[0.7rem] hover:border-primary" title="{{ $event['name'] }} · {{ $event['time'] }}">
                                                    <span class="font-medium">{{ $event['time'] }}</span> {{ $event['name'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-lg border border-dashed bg-muted/20 p-4">
                    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold">Add a time slot</p>
                            <p class="text-sm text-muted-foreground">Add a row to this calendar, then click its cells to place lessons or events.</p>
                        </div>
                        <span class="rounded-full border bg-background px-3 py-1 text-xs text-muted-foreground">
                            {{ $timetable->academicPeriod?->displayName ?? 'Academic period' }}
                        </span>
                    </div>

                    <form wire:submit="addTimeSlot" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6 xl:items-end">
                        <div class="min-w-0 space-y-2">
                            <april:label for="start-time">Starts</april:label>
                            <input type="time" id="start-time" wire:model="startTime"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @error('startTime') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>
                        <div class="min-w-0 space-y-2">
                            <april:label for="stop-time">Ends</april:label>
                            <input type="time" id="stop-time" wire:model="stopTime"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @error('stopTime') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>
                        <div class="min-w-0 space-y-2">
                            <april:label for="slot-recurrence">Repeat</april:label>
                            <select id="slot-recurrence" wire:model.live="slotRecurrence"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                <option value="weekly">Every week(s)</option>
                                <option value="monthly">Every month(s)</option>
                                <option value="one_time">One date only</option>
                            </select>
                            @error('slotRecurrence') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>
                        @if ($slotRecurrence !== 'one_time')
                            <div class="min-w-0 space-y-2">
                                <april:label for="slot-recurrence-interval">Repeats every</april:label>
                                <div class="flex items-center gap-2">
                                    <input type="number" id="slot-recurrence-interval" min="1" max="52" wire:model.number="slotRecurrenceInterval"
                                        class="flex h-10 w-20 rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <span class="text-sm text-muted-foreground">{{ $slotRecurrence === 'monthly' ? 'month(s)' : 'week(s)' }}</span>
                                </div>
                                @error('slotRecurrenceInterval') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                            <div class="min-w-0 space-y-2">
                                <april:label for="slot-starts-on">Starts on</april:label>
                                <input type="date" id="slot-starts-on" wire:model.live="slotStartsOn"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('slotStartsOn') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        @endif
                        @if ($slotRecurrence === 'one_time')
                            <div class="min-w-0 space-y-2">
                                <april:label for="slot-occurs-on">Date</april:label>
                                <input type="date" id="slot-occurs-on" wire:model="slotOccursOn"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('slotOccursOn') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        @endif
                        <div class="min-w-0 md:col-span-2 xl:col-span-1">
                            <april:button type="submit" class="w-full">
                                <x-lucide-plus class="mr-2 size-4" />
                                Add time slot
                            </april:button>
                        </div>
                        @if ($slotRecurrence === 'weekly')
                            <fieldset class="space-y-2 md:col-span-2 xl:col-span-6">
                                <legend class="text-sm font-medium">On these weekdays</legend>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($weekdayMap as $weekdayName => $weekdayId)
                                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border bg-background px-3 py-2 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                            <input type="checkbox" wire:model="slotWeekdayIds" value="{{ $weekdayId }}" class="size-4 rounded border-input text-primary focus:ring-primary">
                                            {{ $weekdayName }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('slotWeekdayIds') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </fieldset>
                        @endif
                        @if ($slotRecurrence !== 'one_time')
                            <p class="text-xs text-muted-foreground md:col-span-2 xl:col-span-6">
                                This rule runs from {{ $slotStartsOn ?: 'the start date' }} until {{ $timetable->academicPeriod?->ends_on?->toDateString() ?? 'the term ends' }}. Change the term dates later and this recurring slot follows them.
                            </p>
                        @endif
                    </form>
                </div>

                <div wire:loading class="text-xs text-muted-foreground">Saving…</div>
            </div>
        </slot:content>
    </april:card>

    @if ($this->selectedLabel !== null)
        <april:card class="border-primary">
            <slot:title>{{ $this->selectedLabel }}</slot:title>
            <slot:description>Choose a subject to teach then, or a part of the day that is not a lesson.</slot:description>
            <slot:content>
                <div class="space-y-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex min-w-56 flex-1 flex-col gap-2">
                            <april:label for="timetable-search">Find a subject or a break</april:label>
                            <april:input id="timetable-search" wire:model.live.debounce.200ms="search" placeholder="Type to narrow the list" />
                        </div>
                        @php ($places = $this->facilities())
                        @if ($places->isNotEmpty())
                            <div class="flex min-w-56 flex-col gap-2">
                                <label for="timetable-facility" class="text-sm font-medium leading-none">Where</label>
                                <select id="timetable-facility" wire:model="facilityId"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                    <option value="">The section's own room</option>
                                    @foreach ($places as $place)
                                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <april:button type="button" variant="outline" wire:click="clearCell">
                            <x-lucide-eraser class="mr-2 size-4" />
                            Leave it empty
                        </april:button>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium">Subjects</p>
                            @if ($this->subjects->isEmpty())
                                <p class="mt-2 text-sm text-muted-foreground">
                                    No subject is offered to this {{ strtolower(school_term('section', 'section')) }}{{ $search === '' ? '' : ' under that search' }}.
                                </p>
                            @else
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($this->subjects as $subject)
                                        <april:button type="button" variant="outline" size="sm"
                                            wire:click="assign('subject', {{ $subject->id }})">
                                            {{ $subject->name }}
                                        </april:button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-medium">Not a lesson</p>
                            @if ($this->customItems->isEmpty())
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Break, assembly, and registration are custom items.
                                    <a class="underline" href="{{ route('custom-timetable-items.index') }}">Add one</a>.
                                </p>
                            @else
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($this->customItems as $item)
                                        <april:button type="button" variant="secondary" size="sm"
                                            wire:click="assign('customTimetableItem', {{ $item->id }})">
                                            {{ $item->name }}
                                        </april:button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </slot:content>
        </april:card>
    @endif

    <april:card>
        <slot:title>Time slots</slot:title>
        <slot:description>Weekly slots follow the term dates. One-date slots stay fixed and are flagged if the term moves.</slot:description>
        <slot:content>
            @if ($this->timeSlots->isEmpty())
                <p class="text-sm text-muted-foreground">No time slots yet. Add one from the calendar above.</p>
            @else
                <ul class="divide-y rounded-md border">
                    @foreach ($this->timeSlots as $slot)
                        <li wire:key="time-slot-{{ $slot->id }}" class="flex items-center justify-between gap-3 px-4 py-2 text-sm">
                            <span><span class="font-medium">{{ $slot->start_time }} to {{ $slot->stop_time }}</span><span class="ml-2 text-muted-foreground">{{ $this->slotRuleLabel($slot) }}</span></span>
                            <april:button type="button" variant="ghost" size="sm"
                                wire:click="removeTimeSlot({{ $slot->id }})"
                                wire:confirm="Remove this time slot and everything placed in it?">
                                <x-lucide-trash-2 class="size-4" />
                                <span class="sr-only">Remove {{ $slot->start_time }} to {{ $slot->stop_time }}</span>
                            </april:button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </slot:content>
    </april:card>
</div>
