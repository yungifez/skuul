<form wire:submit="save" class="flex flex-col gap-8" aria-label="Health record">
    @php
        $controlClasses = 'mt-1 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-60';
        $fields = [
            ['name' => 'blood_group', 'hint' => 'Leave empty when the school has not been told', 'type' => 'text', 'max' => 10],
            ['name' => 'emergency_contact_name', 'hint' => 'The person to call first', 'type' => 'text', 'max' => 100],
            ['name' => 'emergency_contact_phone', 'hint' => null, 'type' => 'tel', 'max' => 50],
            ['name' => 'emergency_contact_relationship', 'hint' => null, 'type' => 'text', 'max' => 50],
        ];
        $notes = [
            ['name' => 'conditions', 'hint' => 'Anything a first aider must know', 'max' => 2000],
            ['name' => 'allergies', 'hint' => 'Say what happens and what to do', 'max' => 2000],
            ['name' => 'medications', 'hint' => 'What the child takes, and when', 'max' => 2000],
            ['name' => 'dietary_needs', 'hint' => null, 'max' => 2000],
            ['name' => 'notes', 'hint' => null, 'max' => 5000],
        ];
        $labels = App\Livewire\HealthRecordForm::LABELS;
    @endphp

    <p class="flex items-start gap-2 text-sm text-muted-foreground">
        <x-lucide-lock class="mt-0.5 size-4 shrink-0" aria-hidden="true" />
        <span>
            {{ $enrollment->admission_number }} ·
            @if ($record === null)
                the school holds nothing for this child yet.
            @else
                last saved {{ school_time($record->updated_at)?->format('j M Y, H:i') }} by {{ $record->updatedBy?->name ?? '—' }}.
            @endif
            The audit log keeps your name and which fields changed, never what they say.
        </span>
    </p>

    @if ($errors->any())
        <p role="alert" class="text-sm font-medium text-destructive">The health record was not saved. {{ $errors->first() }}</p>
    @endif

    <section class="flex flex-col gap-4" aria-labelledby="emergency-heading">
        <h2 id="emergency-heading" class="text-base font-semibold">In an emergency</h2>
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($fields as $field)
                <div wire:key="field-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}" class="text-sm text-muted-foreground">{{ $labels[$field['name']] }}</label>
                    <input type="{{ $field['type'] }}" id="{{ $field['name'] }}" wire:model="values.{{ $field['name'] }}" maxlength="{{ $field['max'] }}" @disabled(!$canWrite) class="{{ $controlClasses }} h-11" {{ field_error_bindings('values.'.$field['name']) }}>
                    @if ($field['hint'] !== null)
                        <p class="mt-1 text-xs text-muted-foreground">{{ $field['hint'] }}</p>
                    @endif
                    <x-field-error :name="'values.'.$field['name']" class="mt-1" />
                </div>
            @endforeach
        </div>
    </section>

    <section class="flex flex-col gap-4" aria-labelledby="needs-heading">
        <div>
            <h2 id="needs-heading" class="text-base font-semibold">What the school must know</h2>
            <p class="text-sm text-muted-foreground">Write only what the school needs to keep the child safe.</p>
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($notes as $note)
                <div wire:key="note-{{ $note['name'] }}">
                    <label for="{{ $note['name'] }}" class="text-sm text-muted-foreground">{{ $labels[$note['name']] }}</label>
                    <textarea id="{{ $note['name'] }}" wire:model="values.{{ $note['name'] }}" rows="3" maxlength="{{ $note['max'] }}" @disabled(!$canWrite) class="{{ $controlClasses }} py-2" {{ field_error_bindings('values.'.$note['name']) }}></textarea>
                    @if ($note['hint'] !== null)
                        <p class="mt-1 text-xs text-muted-foreground">{{ $note['hint'] }}</p>
                    @endif
                    <x-field-error :name="'values.'.$note['name']" class="mt-1" />
                </div>
            @endforeach
        </div>
    </section>

    @if ($canWrite)
        <div class="flex justify-end">
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save the record</april:button>
        </div>
    @else
        <p class="text-sm text-muted-foreground">You may read this record but not change it.</p>
    @endif
</form>
