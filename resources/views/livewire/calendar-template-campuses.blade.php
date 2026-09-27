<div class="flex flex-col gap-8">
    @php($controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring')

    <section class="flex flex-col gap-3" aria-labelledby="generate-year-heading">
        <div>
            <h2 id="generate-year-heading" class="font-semibold">Draft a campus school year</h2>
            <p class="text-sm text-muted-foreground">The school year starts as a draft. Staff review and schedule it before it opens.</p>
        </div>

        @if ($campuses->isEmpty())
            <p class="text-sm text-muted-foreground">This organization has no campuses yet.</p>
        @else
            <form wire:submit="generate" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-start" aria-label="Draft a campus school year">
                <div>
                    <label for="generate-school" class="text-sm text-muted-foreground">Campus</label>
                    <select id="generate-school" wire:model="schoolId" required class="{{ $controlClasses }}" {{ field_error_bindings('schoolId') }}>
                        @foreach ($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="schoolId" class="mt-1" />
                </div>
                <div>
                    <label for="generate-starts-on" class="text-sm text-muted-foreground">School year starts on</label>
                    <input id="generate-starts-on" type="date" wire:model="startsOn" required class="{{ $controlClasses }}" {{ field_error_bindings('startsOn') }}>
                    <x-field-error name="startsOn" class="mt-1" />
                </div>
                <april:button type="submit" class="h-11 select-none sm:mt-6" wire:loading.attr="disabled" wire:target="generate">Draft the school year</april:button>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="campus-adoption-heading">
        <div>
            <h2 id="campus-adoption-heading" class="font-semibold">Campuses</h2>
            <p class="text-sm text-muted-foreground">Point a campus at this template only when it deliberately differs from the organization default.</p>
        </div>

        @if ($campuses->isNotEmpty())
            <ul class="divide-y border-y">
                @foreach ($campuses as $campus)
                    @php($followsThis = $campus->calendar_template_id === $calendarTemplate->id)
                    <li wire:key="campus-{{ $campus->id }}" class="flex flex-col gap-3 py-3">
                        <div class="flex min-h-11 flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium">{{ $campus->name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    @if ($followsThis)
                                        Follows this template
                                    @elseif ($campus->calendarTemplate)
                                        Follows {{ $campus->calendarTemplate->name }}
                                    @else
                                        Follows the organization default
                                    @endif
                                </p>
                            </div>
                            @if ($changingSchoolId !== $campus->id)
                                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startChanging({{ $campus->id }})">
                                    {{ $followsThis ? 'Return to default' : 'Use this template' }}
                                </april:button>
                            @endif
                        </div>

                        @if ($changingSchoolId === $campus->id)
                            <form wire:submit="saveChange" class="flex flex-col gap-3" aria-label="{{ $followsThis ? 'Return '.$campus->name.' to the default calendar' : 'Point '.$campus->name.' at '.$calendarTemplate->name }}">
                                <div>
                                    <label for="campus-reason-{{ $campus->id }}" class="text-sm text-muted-foreground">Why</label>
                                    <input id="campus-reason-{{ $campus->id }}" type="text" wire:model="reason" required maxlength="500" autocomplete="off" placeholder="{{ $followsThis ? 'The campus realigned with the organization.' : 'This campus runs trimesters.' }}" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                                    <x-field-error name="reason" class="mt-1" />
                                </div>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancelChanging">Cancel</april:button>
                                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveChange">
                                        {{ $followsThis ? 'Return to default' : 'Use this template' }}
                                    </april:button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
