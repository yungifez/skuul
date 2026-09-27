<form wire:submit="save" class="flex flex-col gap-6" aria-label="{{ $academicCycleSection === null ? 'Add a '.strtolower(school_term('section', 'section')) : 'Change '.$academicCycleSection->name }}">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $sectionWord = strtolower(school_term('section', 'section'));
        $yearWord = strtolower(school_term('academic_year', 'school year'));
        $classWord = strtolower(school_term('class_level', 'class'));
        $optionalFields = [
            'label' => ['Local label', 255, 'Primary 4 Green'],
            'stream' => ['Stream', 100, 'Science'],
            'shift' => ['Shift', 100, 'Morning'],
            'language' => ['Language of instruction', 100, 'English'],
            'room' => ['Room', 100, 'Block B, room 4'],
        ];
    @endphp

    @if ($academicCycleSection === null)
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="section-year" class="text-sm text-muted-foreground">{{ school_term('academic_year', 'School year') }}</label>
                <select id="section-year" wire:model="academicYearId" required aria-describedby="section-year-hint" class="{{ $controlClasses }}" {{ field_error_bindings('academicYearId') }}>
                    <option value="">Choose a {{ $yearWord }}</option>
                    @foreach ($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                    @endforeach
                </select>
                <p id="section-year-hint" class="mt-1 text-xs text-muted-foreground">The {{ $sectionWord }} serves this {{ $yearWord }} only.</p>
                <x-field-error name="academicYearId" class="mt-1" />
            </div>
            <div>
                <label for="section-level" class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</label>
                <select id="section-level" wire:model="academicLevelId" required class="{{ $controlClasses }}" {{ field_error_bindings('academicLevelId') }}>
                    <option value="">Choose a {{ $classWord }}</option>
                    @foreach ($academicLevels as $academicLevel)
                        <option value="{{ $academicLevel->id }}">{{ $academicLevel->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="academicLevelId" class="mt-1" />
            </div>
        </div>
    @else
        <dl class="grid gap-4 border-y py-3 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-muted-foreground">{{ school_term('academic_year', 'School year') }}</dt>
                <dd class="font-medium">{{ $academicCycleSection->academicYear->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</dt>
                <dd class="font-medium">{{ $academicCycleSection->academicLevel->name }}</dd>
            </div>
        </dl>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="section-name" class="text-sm text-muted-foreground">{{ school_term('section', 'Section') }} name</label>
            <input id="section-name" type="text" wire:model="name" required maxlength="255" autocomplete="off" placeholder="Green" aria-describedby="section-name-hint" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <p id="section-name-hint" class="mt-1 text-xs text-muted-foreground">The short name staff say out loud, such as “Green” or “A”.</p>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="section-teacher" class="text-sm text-muted-foreground">{{ school_term('homeroom_teacher', 'Class teacher') }}</label>
            <select id="section-teacher" wire:model="homeroomTeacherId" class="{{ $controlClasses }}" {{ field_error_bindings('homeroomTeacherId') }}>
                <option value="">Not chosen yet</option>
                @if ($departedTeacher)
                    <option value="{{ $departedTeacher->id }}">{{ $departedTeacher->name }} (no longer at this school)</option>
                @endif
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>
            <x-field-error name="homeroomTeacherId" class="mt-1" />
        </div>
    </div>

    <details class="border-y py-3" @if ($errors->hasAny(array_merge(array_keys($optionalFields), ['capacity', 'position']))) open @endif>
        <summary class="flex min-h-11 cursor-pointer select-none items-center text-sm font-medium">Optional details</summary>
        <div class="mt-3 grid gap-4 md:grid-cols-2">
            @foreach ($optionalFields as $field => [$fieldLabel, $maxLength, $placeholder])
                <div>
                    <label for="section-{{ $field }}" class="text-sm text-muted-foreground">{{ $fieldLabel }}</label>
                    <input id="section-{{ $field }}" type="text" wire:model="{{ $field }}" maxlength="{{ $maxLength }}" autocomplete="off" placeholder="{{ $placeholder }}" class="{{ $controlClasses }}" {{ field_error_bindings($field) }}>
                    <x-field-error :name="$field" class="mt-1" />
                </div>
            @endforeach
            <div>
                <label for="section-capacity" class="text-sm text-muted-foreground">Capacity</label>
                <input id="section-capacity" type="number" wire:model="capacity" min="1" max="999" inputmode="numeric" placeholder="No limit" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('capacity') }}>
                <x-field-error name="capacity" class="mt-1" />
            </div>
            <div>
                <label for="section-position" class="text-sm text-muted-foreground">Display order</label>
                <input id="section-position" type="number" wire:model="position" required min="0" max="9999" inputmode="numeric" class="{{ $controlClasses }} tabular-nums" {{ field_error_bindings('position') }}>
                <x-field-error name="position" class="mt-1" />
            </div>
        </div>
    </details>

    <div class="flex flex-wrap justify-end gap-2">
        <april:button-link href="{{ $academicCycleSection === null ? route('academic-cycle-sections.index') : route('academic-cycle-sections.show', $academicCycleSection) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">{{ $academicCycleSection === null ? 'Create draft '.$sectionWord : 'Save changes' }}</april:button>
    </div>
</form>
