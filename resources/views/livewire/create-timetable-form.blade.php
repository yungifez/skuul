@php
    $selectedPeriod = collect($periods)->firstWhere('id', $academicPeriodId);
    $calendarAnchor = \Illuminate\Support\Carbon::parse($calendarDate ?: now()->toDateString());
    $weekStart = $calendarAnchor->copy()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);
    $monthStart = $calendarAnchor->copy()->startOfMonth();
    $monthCursor = $monthStart->copy()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);
    $draftEvents = collect($events);
@endphp

<div class="space-y-6">
    @if ($periods === [])
        <april:card>
            <slot:title>Set up the school calendar first</slot:title>
            <slot:description>A timetable needs an academic period with dates.</slot:description>
            <slot:content><april:button-link href="{{ route('academic-years.index') }}">Open academic calendar</april:button-link></slot:content>
        </april:card>
    @else
        <april:card>
            <slot:title>Build a timetable</slot:title>
            <slot:description>Choose the term and calendar scope. Recurring entries follow the selected term automatically.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <label for="timetable-name" class="text-sm font-medium">Timetable name *</label>
                        <input id="timetable-name" wire:model="name" placeholder="e.g. Term 1 teaching timetable" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="academic-period" class="text-sm font-medium">Academic period *</label>
                        <select id="academic-period" wire:model.live="academicPeriodId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            @foreach ($periods as $period)
                                <option value="{{ $period['id'] }}">{{ $period['name'] }}{{ $period['starts_on'] ? ' · '.$period['starts_on'].' to '.$period['ends_on'] : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="timetable-scope" class="text-sm font-medium">Schedule for *</label>
                        <select id="timetable-scope" wire:model.live="scope" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="section">One class section</option>
                            @if ($canCreateSchoolwide)<option value="schoolwide">Schoolwide</option>@endif
                        </select>
                        @error('scope') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                    @if ($scope === 'section')
                        <div class="flex flex-col gap-2">
                            <label for="academic-cycle-section" class="text-sm font-medium">Section *</label>
                            <select id="academic-cycle-section" wire:model="academicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                @foreach ($cycleSections as $section)<option value="{{ $section['id'] }}">{{ $section['label'] }}</option>@endforeach
                            </select>
                        </div>
                    @endif
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <label for="timetable-description" class="text-sm font-medium">Description</label>
                        <textarea id="timetable-description" wire:model="description" rows="2" placeholder="Optional context for staff" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Add calendar events</slot:title>
            <slot:description>Click a date to add a one-time event, or choose one or more weekdays for a recurring event.</slot:description>
            <slot:content>
                <div class="space-y-5">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6 xl:items-end">
                        <div class="flex flex-col gap-2"><label for="event-recurrence" class="text-sm font-medium">Repeat</label><select id="event-recurrence" wire:model.live="newEvent.recurrence" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="weekly">Every week(s)</option><option value="monthly">Every month(s)</option><option value="one_time">One date only</option></select></div>
                        @if ($newEvent['recurrence'] !== 'one_time')
                            <div class="flex flex-col gap-2"><label for="event-recurrence-interval" class="text-sm font-medium">Repeats every</label><div class="flex items-center gap-2"><input id="event-recurrence-interval" type="number" min="1" max="52" wire:model.number="newEvent.recurrence_interval" class="h-10 w-20 rounded-md border border-input bg-background px-3 text-sm"><span class="text-sm text-muted-foreground">{{ $newEvent['recurrence'] === 'monthly' ? 'month(s)' : 'week(s)' }}</span></div>@error('newEvent.recurrence_interval') <p class="text-sm text-destructive">{{ $message }}</p> @enderror</div>
                            <div class="flex flex-col gap-2"><label for="event-starts-on" class="text-sm font-medium">Starts on</label><input id="event-starts-on" type="date" wire:model.live="newEvent.starts_on" class="h-10 rounded-md border border-input bg-background px-3 text-sm">@error('newEvent.starts_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror</div>
                        @endif
                        @if ($newEvent['recurrence'] === 'weekly')
                            <div class="flex flex-col gap-2 md:col-span-2 xl:col-span-3">
                                <span class="text-sm font-medium">On weekdays</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($weekdays as $weekday)
                                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                            <input type="checkbox" wire:model="newEvent.weekday_ids" value="{{ $weekday['id'] }}" class="rounded border-input text-primary focus:ring-primary">
                                            {{ $weekday['name'] }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('newEvent.weekday_ids') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div class="flex flex-col gap-2 md:col-span-2"><label for="event-occurs-on" class="text-sm font-medium">Date *</label><input id="event-occurs-on" type="date" wire:model.live="newEvent.occurs_on" class="h-10 rounded-md border border-input bg-background px-3 text-sm">@error('newEvent.occurs_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror</div>
                        @endif
                        <div class="flex flex-col gap-2"><label for="event-start" class="text-sm font-medium">Starts</label><input id="event-start" type="time" wire:model="newEvent.start_time" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                        <div class="flex flex-col gap-2"><label for="event-stop" class="text-sm font-medium">Ends</label><input id="event-stop" type="time" wire:model="newEvent.stop_time" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                        <div class="flex flex-col gap-2"><label for="event-type" class="text-sm font-medium">Event type</label><select id="event-type" wire:model.live="newEvent.type" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="subject">Subject lesson</option><option value="role">Role event</option><option value="freehand">Freehand</option></select></div>
                        @if ($newEvent['type'] === 'subject')
                            <div class="flex flex-col gap-2 xl:col-span-2"><label for="event-subject" class="text-sm font-medium">Subject</label><select id="event-subject" wire:model="newEvent.subject_id" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="">Choose a subject</option>@foreach ($subjects as $subject)<option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>@endforeach</select></div>
                        @else
                            <div class="flex flex-col gap-2"><label for="event-title" class="text-sm font-medium">Title</label><input id="event-title" wire:model="newEvent.title" placeholder="Assembly, duty, club…" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                        @endif
                        @if ($newEvent['type'] === 'role')
                            <div class="flex flex-col gap-2"><label for="event-role" class="text-sm font-medium">Visible to</label><select id="event-role" wire:model="newEvent.audience_role" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="">Choose a role</option>@foreach ($roles as $role)<option value="{{ $role['id'] }}">{{ $role['name'] }}</option>@endforeach</select></div>
                        @endif
                    </div>

                    <div class="rounded-md border border-primary/20 bg-primary/5 p-3 text-sm">
                        <p class="font-medium">Term-scoped recurrence</p>
                        <p class="mt-1 text-muted-foreground">{{ $selectedPeriod['name'] ?? 'Selected period' }} runs from {{ $selectedPeriod['starts_on'] ?? 'its start date' }} to {{ $selectedPeriod['ends_on'] ?? 'its end date' }}. Recurring events follow these dates if the term is moved later.</p>
                        @if ($newEvent['recurrence'] !== 'one_time')
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div class="flex flex-col gap-1"><label for="event-term-start" class="text-xs font-medium text-muted-foreground">Recurrence starts</label><input id="event-term-start" type="date" value="{{ $newEvent['starts_on'] ?? '' }}" readonly class="h-9 rounded-md border border-input bg-background px-3 text-sm text-muted-foreground"></div>
                                <div class="flex flex-col gap-1"><label for="event-term-end" class="text-xs font-medium text-muted-foreground">Recurrence ends</label><input id="event-term-end" type="date" value="{{ $selectedPeriod['ends_on'] ?? '' }}" readonly class="h-9 rounded-md border border-input bg-background px-3 text-sm text-muted-foreground"></div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end"><button type="button" wire:click="addEvent" class="inline-flex h-10 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Add to calendar</button></div>
                    @error('newEvent.*') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Calendar preview</slot:title>
            <slot:description>{{ count($events) }} {{ Str::plural('event', count($events)) }} staged. Click a date in Month view to prefill a one-time event.</slot:description>
            <slot:content>
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border bg-muted/20 p-3">
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="moveCalendar(-1)" class="inline-flex size-9 items-center justify-center rounded-md border bg-background hover:bg-muted" aria-label="Previous calendar period">‹</button>
                            <button type="button" wire:click="moveCalendar(1)" class="inline-flex size-9 items-center justify-center rounded-md border bg-background hover:bg-muted" aria-label="Next calendar period">›</button>
                            <button type="button" wire:click="goToCalendarToday" class="inline-flex h-9 items-center rounded-md border bg-background px-3 text-sm font-medium hover:bg-muted">Today</button>
                            <span class="ml-1 text-sm font-semibold">{{ $calendarView === 'month' ? $calendarAnchor->format('F Y') : $weekStart->format('j M').' – '.$weekStart->copy()->addDays(6)->format('j M Y') }}</span>
                        </div>
                        <div class="flex rounded-md border bg-background p-1">
                            @foreach (['week' => 'Week', 'month' => 'Month'] as $view => $label)
                                <button type="button" wire:click="setCalendarView('{{ $view }}')" class="rounded px-3 py-1.5 text-sm {{ $calendarView === $view ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted' }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>

                    @if ($calendarView === 'week')
                        <div class="grid min-w-[48rem] grid-cols-7 gap-px overflow-x-auto rounded-md border bg-border">
                            @foreach (range(0, 6) as $dayOffset)
                                @php
                                    $date = $weekStart->copy()->addDays($dayOffset);
                                    $weekdayId = data_get(collect($weekdays)->firstWhere('name', $date->englishDayOfWeek), 'id');
                                    $dayEvents = $draftEvents->filter(fn (array $event): bool => $this->eventOccursOn($event, $date));
                                @endphp
                                <div wire:key="draft-week-day-{{ $date->toDateString() }}" class="min-h-48 bg-background p-2 {{ $date->toDateString() === $calendarDate ? 'ring-2 ring-inset ring-primary' : '' }}">
                                    <button type="button" wire:click="chooseCalendarDate('{{ $date->toDateString() }}')" class="flex w-full items-center justify-between rounded px-1 py-1 text-left hover:bg-muted"><span class="text-xs font-semibold">{{ $date->format('D') }}</span><span class="text-xs text-muted-foreground">{{ $date->format('j M') }}</span></button>
                                    <div class="mt-3 space-y-2">
                                        @forelse ($dayEvents->sortBy('start_time') as $event)
                                            @php
                                                $eventIndex = array_search($event, $events, true);
                                            @endphp
                                            <div wire:key="draft-week-event-{{ $date->toDateString() }}-{{ $eventIndex }}" class="rounded-md border border-primary/20 bg-primary/10 p-2 text-xs"><div class="flex items-start justify-between gap-2"><span class="font-medium">{{ $event['start_time'] }}–{{ $event['stop_time'] }}</span><button type="button" wire:click="removeEvent({{ $eventIndex }})" class="text-muted-foreground hover:text-destructive" aria-label="Remove event">×</button></div><p class="mt-1">{{ $event['type'] === 'subject' ? data_get(collect($subjects)->firstWhere('id', $event['subject_id']), 'name', 'Subject') : $event['title'] }}</p><p class="mt-1 text-muted-foreground">{{ $event['recurrence'] === 'weekly' ? 'Every week · '.$date->format('D') : 'One date' }}</p></div>
                                        @empty
                                            <p class="pt-4 text-center text-xs text-muted-foreground">No events</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="grid min-w-[48rem] grid-cols-7 gap-px overflow-x-auto rounded-md border bg-border">
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $dayName)
                                <div class="bg-muted/60 px-2 py-2 text-center text-xs font-semibold">{{ $dayName }}</div>
                            @endforeach
                            @foreach (range(0, 41) as $index)
                                @php
                                    $date = $monthCursor->copy()->addDays($index);
                                    $weekdayId = data_get(collect($weekdays)->firstWhere('name', $date->englishDayOfWeek), 'id');
                                    $dayEvents = $draftEvents->filter(fn (array $event): bool => $this->eventOccursOn($event, $date));
                                @endphp
                                <div wire:key="draft-month-day-{{ $date->toDateString() }}" class="min-h-24 bg-background p-2 {{ $date->month !== $monthStart->month ? 'bg-muted/20 text-muted-foreground' : '' }}">
                                    <button type="button" wire:click="chooseCalendarDate('{{ $date->toDateString() }}')" class="flex size-7 items-center justify-center rounded-full text-xs font-semibold hover:bg-primary hover:text-primary-foreground {{ $date->toDateString() === $calendarDate ? 'bg-primary text-primary-foreground' : '' }}">{{ $date->day }}</button>
                                    <div class="mt-1 space-y-1">
                                        @foreach ($dayEvents as $event)
                                            <div wire:key="draft-month-event-{{ $date->toDateString() }}-{{ $loop->index }}" class="truncate rounded border border-primary/20 bg-primary/10 px-1 py-0.5 text-[0.65rem]" title="{{ $event['title'] ?: 'Subject' }}">{{ $event['start_time'] }} · {{ $event['type'] === 'subject' ? data_get(collect($subjects)->firstWhere('id', $event['subject_id']), 'name', 'Subject') : $event['title'] }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <div class="flex justify-end"><button type="button" wire:click="save" wire:loading.attr="disabled" class="inline-flex h-10 items-center rounded-md bg-primary px-5 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"><span wire:loading.remove wire:target="save">Create timetable</span><span wire:loading wire:target="save">Creating…</span></button></div>
    @endif
</div>
