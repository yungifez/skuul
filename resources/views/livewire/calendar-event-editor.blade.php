<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $chipClasses = 'flex min-h-11 cursor-pointer items-center gap-2 rounded-md border px-3 text-sm select-none has-[:checked]:border-foreground has-[:checked]:font-medium';
        $chosenType = \App\Enums\CalendarEventType::tryFrom($type);
        $canPublish = $event !== null && auth()->user()->can('publish', $event);
        $canRemove = $event !== null && auth()->user()->can('delete', $event);
    @endphp

    @if ($event !== null)
        <div class="flex flex-wrap items-center justify-between gap-3 border-y py-3">
            <p class="text-sm">
                <span class="font-medium">{{ $event->is_published ? 'On the calendar' : 'Draft' }}</span>
                <span class="text-muted-foreground">· {{ $event->isTeachingDay() ? 'the school teaches' : 'the school is shut' }}{{ $event->is_published ? '' : ' once published' }}</span>
            </p>
            <div class="flex items-center gap-2">
                @if ($canPublish && !$event->is_published)
                    <april:button type="button" class="h-11 select-none" wire:click="publish" wire:loading.attr="disabled" wire:target="publish">
                        <x-lucide-send class="mr-2 size-4" aria-hidden="true" />Publish
                    </april:button>
                @endif
                @if (($canPublish && $event->is_published) || $canRemove)
                    <april:dropdown-menu>
                        <slot:trigger>
                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $event->title }}">
                                <x-lucide-ellipsis class="size-4" />
                            </april:button>
                        </slot:trigger>
                        <slot:content align="end">
                            @if ($canPublish && $event->is_published)
                                <april:dropdown-menu-item wire:click="unpublish"><x-lucide-eye-off class="mr-2 size-4" />Make it a draft again</april:dropdown-menu-item>
                            @endif
                            @if ($canRemove)
                                <april:dropdown-menu-item class="text-destructive" wire:click="remove" wire:confirm="Remove {{ $event->title }} from the calendar?">
                                    <x-lucide-trash-2 class="mr-2 size-4" />Remove
                                </april:dropdown-menu-item>
                            @endif
                        </slot:content>
                    </april:dropdown-menu>
                @endif
            </div>
        </div>
    @endif

    <form wire:submit="save" class="flex flex-col gap-8" aria-label="{{ $event === null ? 'Add a day' : 'Change '.$event->title }}">
        <section class="flex flex-col gap-3" aria-label="What is on">
            <div>
                <label for="event-title" class="sr-only">Title</label>
                <input id="event-title" wire:model="title" maxlength="255" required placeholder="Title, e.g. mid-term break" class="{{ $controlClasses }}" {{ field_error_bindings('title') }}>
                <x-field-error name="title" class="mt-1" />
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="event-type" class="sr-only">Kind of day</label>
                    <select id="event-type" wire:model.live="type" class="{{ $controlClasses }}" {{ field_error_bindings('type') }}>
                        @foreach ($types as $eventType)
                            <option value="{{ $eventType->value }}">{{ $eventType->label() }}{{ $eventType->isTeachingDay() ? '' : ' — school shut' }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="type" class="mt-1" />
                </div>
                <div>
                    <label for="event-location" class="sr-only">Where (optional)</label>
                    <input id="event-location" wire:model="location" maxlength="255" placeholder="Where (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('location') }}>
                    <x-field-error name="location" class="mt-1" />
                </div>
            </div>
            @if ($chosenType !== null && !$chosenType->isTeachingDay())
                <p class="flex items-center gap-2 text-sm text-muted-foreground">
                    <x-lucide-lock class="size-4" aria-hidden="true" />Once published, attendance and the timetable treat these days as shut.
                </p>
            @endif

            <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                <input type="checkbox" wire:model.live="isAllDay" class="size-4 rounded border-input">
                All day
            </label>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="event-starts" class="text-sm text-muted-foreground">Starts</label>
                    <input type="{{ $isAllDay ? 'date' : 'datetime-local' }}" id="event-starts" wire:key="starts-{{ $isAllDay ? 'day' : 'time' }}" wire:model="startsAt" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('startsAt') }}>
                </div>
                <div>
                    <label for="event-ends" class="text-sm text-muted-foreground">Ends</label>
                    <input type="{{ $isAllDay ? 'date' : 'datetime-local' }}" id="event-ends" wire:key="ends-{{ $isAllDay ? 'day' : 'time' }}" wire:model="endsAt" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('endsAt') }}>
                </div>
            </div>
            <x-field-error name="startsAt" />
            <x-field-error name="endsAt" />

            <div>
                <label for="event-description" class="sr-only">What it is (optional)</label>
                <textarea id="event-description" wire:model="description" rows="3" maxlength="2000" placeholder="What it is (optional)"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
                <x-field-error name="description" />
            </div>
        </section>

        <section class="flex flex-col gap-4" aria-labelledby="audience-heading">
            <div>
                <h2 id="audience-heading" class="text-base font-semibold">Who it is for</h2>
                <p class="text-sm text-muted-foreground">Name nobody and it is for the whole school.</p>
            </div>

            @if ($sections->isEmpty())
                <p class="text-sm text-muted-foreground">No {{ strtolower(school_terms('section', 'sections')) }} this year</p>
            @else
                <div class="flex flex-wrap gap-2" role="group" aria-label="{{ school_terms('section', 'Sections') }}">
                    @foreach ($sections as $section)
                        <label wire:key="section-{{ $section->id }}" class="{{ $chipClasses }}">
                            <input type="checkbox" value="{{ $section->id }}" wire:model="sectionIds" class="size-4 rounded border-input">
                            {{ $section->qualifiedName() }}
                        </label>
                    @endforeach
                </div>
            @endif
            <x-field-error name="sectionIds.*" />

            <div class="flex flex-col gap-2">
                @if ($chosenPeople->isNotEmpty())
                    <ul class="flex flex-wrap gap-2" aria-label="People named">
                        @foreach ($chosenPeople as $person)
                            <li wire:key="person-{{ $person->id }}" class="flex min-h-11 items-center gap-1 rounded-md border pl-3 text-sm">
                                {{ $person->name }}
                                <button type="button" wire:click="removePerson({{ $person->id }})" class="flex size-11 items-center justify-center select-none" aria-label="Remove {{ $person->name }}">
                                    <x-lucide-x class="size-4" aria-hidden="true" />
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <label for="event-person" class="sr-only">Name a person</label>
                <input id="event-person" type="search" wire:model.live.debounce.300ms="personSearch" autocomplete="off" placeholder="Name a person, e.g. for an appointment" class="{{ $controlClasses }}">
                @if (mb_strlen(trim($personSearch)) >= 2)
                    @if ($matches->isEmpty())
                        <p class="text-sm text-muted-foreground">Nobody in this school matches</p>
                    @else
                        <ul class="divide-y border-y" aria-label="Matching people">
                            @foreach ($matches as $person)
                                <li wire:key="match-{{ $person->id }}">
                                    <button type="button" wire:click="addPerson({{ $person->id }})" class="flex min-h-11 w-full items-center gap-2 px-1 text-left text-sm select-none hover:bg-muted/50">
                                        <x-lucide-plus class="size-4" aria-hidden="true" />{{ $person->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
                <x-field-error name="userIds.*" />
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ route('calendar-events.index', ['month' => \Illuminate\Support\Str::substr($startsAt, 0, 7)]) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" :variant="$event !== null && !$event->is_published && $canPublish ? 'outline' : 'default'" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">
                {{ $event === null ? 'Save as a draft' : 'Save' }}
            </april:button>
        </div>
    </form>
</div>
