<div class="space-y-6">
    @if ($periods === [])
        <april:card>
            <slot:title>Set up the school calendar first</slot:title>
            <slot:description>A recurring timetable needs an academic period with dates.</slot:description>
            <slot:content><april:button-link href="{{ route('academic-years.index') }}">Open academic calendar</april:button-link></slot:content>
        </april:card>
    @else
        <april:card>
            <slot:title>Build a recurring timetable</slot:title>
            <slot:description>Choose the term. Every event you add repeats weekly for that period.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <label for="timetable-name" class="text-sm font-medium">Timetable name *</label>
                        <input id="timetable-name" wire:model="name" placeholder="e.g. Term 1 teaching timetable" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="academic-period" class="text-sm font-medium">Repeats during *</label>
                        <select id="academic-period" wire:model="academicPeriodId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            @foreach ($periods as $period)
                                <option value="{{ $period['id'] }}">{{ $period['name'] }}{{ $period['starts_on'] ? ' · '.$period['starts_on'].' to '.$period['ends_on'] : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="timetable-scope" class="text-sm font-medium">Schedule for *</label>
                        <select id="timetable-scope" wire:model.live="scope" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="section">One class section</option><option value="schoolwide">Schoolwide</option>
                        </select>
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
            <slot:title>Add recurring events</slot:title>
            <slot:description>A subject belongs to the selected section. A role event is shown to that staff role. Freehand is for anything else.</slot:description>
            <slot:content>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6 xl:items-end">
                    <div class="flex flex-col gap-2"><label for="event-weekday" class="text-sm font-medium">Day</label><select id="event-weekday" wire:model="newEvent.weekday_id" class="h-10 rounded-md border border-input bg-background px-3 text-sm">@foreach ($weekdays as $weekday)<option value="{{ $weekday['id'] }}">{{ $weekday['name'] }}</option>@endforeach</select></div>
                    <div class="flex flex-col gap-2"><label for="event-start" class="text-sm font-medium">Starts</label><input id="event-start" type="time" wire:model="newEvent.start_time" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                    <div class="flex flex-col gap-2"><label for="event-stop" class="text-sm font-medium">Ends</label><input id="event-stop" type="time" wire:model="newEvent.stop_time" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                    <div class="flex flex-col gap-2"><label for="event-type" class="text-sm font-medium">Event type</label><select id="event-type" wire:model.live="newEvent.type" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="subject">Subject</option><option value="role">Role event</option><option value="freehand">Freehand</option></select></div>
                    @if ($newEvent['type'] === 'subject')
                        <div class="flex flex-col gap-2 xl:col-span-2"><label for="event-subject" class="text-sm font-medium">Subject</label><select id="event-subject" wire:model="newEvent.subject_id" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="">Choose a subject</option>@foreach ($subjects as $subject)<option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>@endforeach</select></div>
                    @else
                        <div class="flex flex-col gap-2"><label for="event-title" class="text-sm font-medium">Title</label><input id="event-title" wire:model="newEvent.title" placeholder="Assembly, duty, club…" class="h-10 rounded-md border border-input bg-background px-3 text-sm"></div>
                    @endif
                    @if ($newEvent['type'] === 'role')
                        <div class="flex flex-col gap-2"><label for="event-role" class="text-sm font-medium">Visible to</label><select id="event-role" wire:model="newEvent.audience_role" class="h-10 rounded-md border border-input bg-background px-3 text-sm"><option value="">Choose a role</option>@foreach ($roles as $role)<option value="{{ $role['id'] }}">{{ $role['name'] }}</option>@endforeach</select></div>
                    @endif
                    <div class="xl:col-span-6"><button type="button" wire:click="addEvent" class="inline-flex h-10 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Add to calendar</button></div>
                </div>
                @error('newEvent.*') <p class="mt-3 text-sm text-destructive">{{ $message }}</p> @enderror
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Weekly calendar preview</slot:title>
            <slot:description>{{ count($events) }} recurring {{ Str::plural('event', count($events)) }} will be created.</slot:description>
            <slot:content>
                @if ($events === [])
                    <p class="rounded-md border border-dashed p-8 text-center text-sm text-muted-foreground">Add an event above to start drawing the week.</p>
                @else
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        @foreach ($weekdays as $weekday)
                            <div wire:key="weekday-{{ $weekday['id'] }}" class="min-h-36 rounded-md border bg-muted/20 p-3"><h3 class="text-sm font-semibold">{{ $weekday['name'] }}</h3><div class="mt-3 space-y-2">
                                @forelse (collect($events)->where('weekday_id', $weekday['id'])->sortBy('start_time') as $index => $event)
                                    <div wire:key="event-{{ $index }}" class="rounded-md border bg-background p-2 text-xs"><div class="flex items-start justify-between gap-2"><span class="font-medium">{{ $event['start_time'] }}–{{ $event['stop_time'] }}</span><button type="button" wire:click="removeEvent({{ $index }})" class="text-muted-foreground hover:text-destructive" aria-label="Remove event">×</button></div><p class="mt-1">{{ $event['type'] === 'subject' ? data_get(collect($subjects)->firstWhere('id', $event['subject_id']), 'name', 'Subject') : $event['title'] }}</p>@if ($event['audience_role'])<p class="mt-1 text-muted-foreground">{{ data_get(collect($roles)->firstWhere('id', $event['audience_role']), 'name', $event['audience_role']) }}</p>@endif</div>
                                @empty <p class="text-xs text-muted-foreground">No events</p>
                                @endforelse
                            </div></div>
                        @endforeach
                    </div>
                @endif
            </slot:content>
        </april:card>

        <div class="flex justify-end"><button type="button" wire:click="save" wire:loading.attr="disabled" class="inline-flex h-10 items-center rounded-md bg-primary px-5 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"><span wire:loading.remove wire:target="save">Create timetable</span><span wire:loading wire:target="save">Creating…</span></button></div>
    @endif
</div>
