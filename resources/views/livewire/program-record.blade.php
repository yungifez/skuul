<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $learnerName = fn ($place) => $place->studentRecord?->user?->name ?? $place->studentRecord?->admission_number ?? '—';
    @endphp

    <section class="flex flex-col gap-3" aria-labelledby="program-heading">
        <div class="flex items-center justify-between gap-3">
            <h2 id="program-heading" class="text-base font-semibold">The programme</h2>
            @if ($canWrite && !$isEditing)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startEditing">
                    <x-lucide-pencil class="mr-2 size-4" aria-hidden="true" />Change
                </april:button>
            @endif
        </div>

        @if ($isEditing)
            <form wire:submit="save" class="flex flex-col gap-3" aria-label="Change the programme">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm text-muted-foreground">Name</label>
                        <input id="name" wire:model="name" required maxlength="100" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                        <x-field-error name="name" class="mt-1" />
                    </div>
                    <div>
                        <label for="description" class="text-sm text-muted-foreground">What it is (optional)</label>
                        <input id="description" wire:model="description" maxlength="1000" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
                        <x-field-error name="description" class="mt-1" />
                    </div>
                </div>
                <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                    <input type="checkbox" wire:model="isActive" class="size-5 rounded border-input">
                    Open. A closed programme gives no new places; the places held keep running.
                </label>
                <div class="flex justify-end gap-2">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopEditing">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save</april:button>
                </div>
            </form>
        @else
            <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-muted-foreground">Kind</dt>
                    <dd>{{ $program->type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">State</dt>
                    <dd>{{ $program->is_active ? 'Open' : 'Closed' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Taking part now</dt>
                    <dd>{{ $running->count() }} of {{ $program->participations->count() }} places given</dd>
                </div>
                <div class="sm:col-span-3">
                    <dt class="text-muted-foreground">What it is</dt>
                    <dd>{{ $program->description ?? '—' }}</dd>
                </div>
            </dl>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="places-heading">
        <h2 id="places-heading" class="text-base font-semibold">Places</h2>

        @if ($program->participations->isEmpty())
            <p class="text-sm text-muted-foreground">Nobody takes part yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($program->participations as $place)
                    <li wire:key="place-{{ $place->id }}" @class(['flex items-center gap-3 py-3', 'text-muted-foreground' => !$place->status->isRunning()])>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $learnerName($place) }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $place->status->label() }} · from {{ $place->starts_on?->format('j M Y') ?? '—' }}{{ $place->ends_on ? ' to '.$place->ends_on->format('j M Y') : '' }}
                                · {{ $place->schedule ?? '—' }} · run by {{ $place->staff?->name ?? '—' }}
                            </p>
                        </div>
                        @if ($canWrite && $place->status->allowedNext() !== [])
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Move the place of {{ $learnerName($place) }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    @foreach ($place->status->allowedNext() as $next)
                                        <april:dropdown-menu-item wire:click="movePlace({{ $place->id }}, '{{ $next->value }}', '{{ $place->status->value }}')">Mark as {{ Str::lower($next->label()) }}</april:dropdown-menu-item>
                                    @endforeach
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canWrite && $program->is_active)
            <form wire:submit="givePlace" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:items-end" aria-label="Give a learner a place">
                <div class="sm:col-span-2">
                    <label for="student_record_id" class="text-sm text-muted-foreground">Learner</label>
                    <select id="student_record_id" wire:model="studentRecordId" required class="{{ $controlClasses }}" {{ field_error_bindings('studentRecordId') }}>
                        <option value="">Choose a learner</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user?->name ?? '—' }} · {{ $student->admission_number }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="studentRecordId" class="mt-1" />
                </div>
                <div>
                    <label for="starts_on" class="text-sm text-muted-foreground">Starts on</label>
                    <input type="date" id="starts_on" wire:model="startsOn" required class="{{ $controlClasses }}" {{ field_error_bindings('startsOn') }}>
                    <x-field-error name="startsOn" class="mt-1" />
                </div>
                <div>
                    <label for="schedule" class="text-sm text-muted-foreground">When it runs (optional)</label>
                    <input id="schedule" wire:model="schedule" maxlength="255" placeholder="Tuesday, 15:30" class="{{ $controlClasses }}" {{ field_error_bindings('schedule') }}>
                    <x-field-error name="schedule" class="mt-1" />
                </div>
                <div class="sm:col-span-2">
                    <label for="staff_id" class="text-sm text-muted-foreground">Run by (optional)</label>
                    <select id="staff_id" wire:model="staffId" class="{{ $controlClasses }}" {{ field_error_bindings('staffId') }}>
                        <option value="">Nobody yet</option>
                        @foreach ($staff as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="staffId" class="mt-1" />
                </div>
                <div class="sm:col-span-2 lg:justify-self-end">
                    <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="givePlace">
                        <x-lucide-user-plus class="mr-2 size-4" aria-hidden="true" />Give a place
                    </april:button>
                </div>
            </form>
        @elseif ($canWrite)
            <p class="text-sm text-muted-foreground">The programme is closed, so it gives no new places.</p>
        @endif
    </section>
</div>
