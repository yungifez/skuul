<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <form wire:submit="save" class="flex flex-col gap-8" aria-label="Record a case">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex flex-col gap-2 sm:col-span-2">
                <label for="summary" class="text-sm font-medium">Summary</label>
                <input id="summary" wire:model="summary" maxlength="255" placeholder="One line that names the event" class="{{ $controlClasses }}" {{ field_error_bindings('summary') }}>
                <x-field-error name="summary" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="category" class="text-sm font-medium">Kind of case</label>
                <select id="category" wire:model.live="category" class="{{ $controlClasses }}" {{ field_error_bindings('category') }}>
                    @foreach ($categories as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
                @if ($isRestricted)
                    <p class="inline-flex items-center gap-1 text-sm text-muted-foreground" id="restricted-mark"><x-lucide-lock class="size-3.5" aria-hidden="true" /> Restricted</p>
                @endif
                <x-field-error name="category" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="occurred-at" class="text-sm font-medium">When</label>
                <input type="datetime-local" id="occurred-at" wire:model="occurredAt" max="{{ school_now()->format('Y-m-d\TH:i') }}" class="{{ $controlClasses }}" {{ field_error_bindings('occurredAt') }}>
                <x-field-error name="occurredAt" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="location" class="text-sm font-medium">Where</label>
                <input id="location" wire:model="location" maxlength="255" placeholder="Optional" class="{{ $controlClasses }}" {{ field_error_bindings('location') }}>
                <x-field-error name="location" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="assigned-to" class="text-sm font-medium">Handled by</label>
                <select id="assigned-to" wire:model="assignedTo" class="{{ $controlClasses }}" {{ field_error_bindings('assignedTo') }}>
                    <option value="">Nobody yet</option>
                    @foreach ($staff as $person)
                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="assignedTo" />
            </div>

            <div class="flex flex-col gap-2 sm:col-span-2">
                <label for="description" class="text-sm font-medium">What happened</label>
                <textarea id="description" wire:model="description" rows="5" maxlength="5000" placeholder="What you saw, not what you think it means"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
                <x-field-error name="description" />
            </div>
        </div>

        <fieldset class="flex flex-col gap-3">
            <legend class="mb-2 text-base font-semibold">People</legend>
            <ul class="divide-y border-y">
                @foreach ($participants as $index => $participant)
                    <li wire:key="participant-row-{{ $index }}" class="grid gap-3 py-3 sm:grid-cols-[1fr_12rem_1fr_auto] sm:items-start">
                        <div>
                            <label for="participant-{{ $index }}-student" class="sr-only">Learner</label>
                            <select id="participant-{{ $index }}-student" wire:model="participants.{{ $index }}.student_record_id" class="{{ $controlClasses }}" {{ field_error_bindings("participants.$index.student_record_id") }}>
                                <option value="">Choose a learner</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}</option>
                                @endforeach
                            </select>
                            <x-field-error name="participants.{{ $index }}.student_record_id" class="mt-1" />
                        </div>
                        <div>
                            <label for="participant-{{ $index }}-role" class="sr-only">Why they appear</label>
                            <select id="participant-{{ $index }}-role" wire:model="participants.{{ $index }}.role" class="{{ $controlClasses }}">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="participant-{{ $index }}-note" class="sr-only">Note</label>
                            <input id="participant-{{ $index }}-note" wire:model="participants.{{ $index }}.note" maxlength="255" placeholder="Note (optional)" class="{{ $controlClasses }}">
                        </div>
                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" wire:click="removeParticipant({{ $index }})" aria-label="Remove row {{ $index + 1 }}">
                            <x-lucide-x class="size-4" />
                        </april:button>
                    </li>
                @endforeach
            </ul>
            @if ($canAddParticipant)
                <div>
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="addParticipant">
                        <x-lucide-plus class="mr-2 size-4" />Add a person
                    </april:button>
                </div>
            @endif
        </fieldset>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ route('incidents.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Record the case</april:button>
        </div>
    </form>
</div>
