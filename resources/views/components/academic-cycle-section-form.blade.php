@props([
    'action',
    'method' => 'POST',
    'section' => null,
    'academicYears' => null,
    'academicLevels' => null,
    'teachers',
    'preselectedAcademicYearId' => null,
    'preselectedAcademicLevelId' => null,
    'submitLabel' => 'Save section',
    'cancelHref',
])

@php
    $value = static fn (string $field, $fallback = null) => old($field, $section?->{$field} ?? $fallback);
    $selected = static fn (string $field, $option, $fallback = null) => (string) old($field, $section?->{$field} ?? $fallback) === (string) $option;
    $hasDetails = session()->hasOldInput() || $errors->any();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <x-display-validation-errors />

    <div class="space-y-4">
        <h3 class="text-sm font-semibold">1. Where the {{ school_term('section', 'section') }} belongs</h3>

        @if ($section === null)
            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <april:label for="academic-year">{{ school_term('academic_year', 'Academic year') }}</april:label>
                    <select id="academic-year" name="academic_year_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                        <option value="">Select a {{ school_term('academic_year', 'school year') }}</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" {{ $selected('academic_year_id', $academicYear->id, $preselectedAcademicYearId) ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-muted-foreground">The {{ school_term('section', 'section') }} serves this {{ school_term('academic_year', 'school year') }} only.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <april:label for="academic-level">{{ school_term('class_level', 'Class') }}</april:label>
                    <select id="academic-level" name="academic_level_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                        <option value="">Select a {{ school_term('class_level', 'class') }}</option>
                        @foreach ($academicLevels as $academicLevel)
                            <option value="{{ $academicLevel->id }}" {{ $selected('academic_level_id', $academicLevel->id, $preselectedAcademicLevelId) ? 'selected' : '' }}>{{ $academicLevel->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-muted-foreground">Only active {{ school_terms('class_level', 'classes') }} are offered.</p>
                </div>
            </div>
        @else
            <div class="grid gap-4 rounded-md border bg-muted/30 p-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-muted-foreground">{{ school_term('academic_year', 'School year') }}</p>
                    <p class="font-medium">{{ $section->academicYear->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</p>
                    <p class="font-medium">{{ $section->academicLevel->name }}</p>
                </div>
                <p class="text-sm text-muted-foreground sm:col-span-2">
                    The {{ school_term('academic_year', 'school year') }} and {{ school_term('class_level', 'class') }} are fixed. A {{ school_term('section', 'section') }} serves one exact {{ school_term('academic_year', 'school year') }}, so a later year needs its own {{ school_term('section', 'section') }}.
                    Use “Roll {{ strtolower(school_terms('section', 'sections')) }} into another {{ strtolower(school_term('academic_year', 'year')) }}” to copy this setup forward.
                </p>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <h3 class="text-sm font-semibold">2. What the {{ school_term('section', 'section') }} is called</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <april:input-group id="name" name="name" label="{{ school_term('section', 'Section') }} name" value="{{ $value('name') }}" required maxlength="255" placeholder="Green" />
            <div class="flex flex-col gap-2">
                <april:label for="homeroom-teacher">{{ school_term('homeroom_teacher', 'Class teacher') }}</april:label>
                <select id="homeroom-teacher" name="homeroom_teacher_id" class="rounded-md border border-input bg-background px-3 py-2">
                    <option value="">Not chosen yet</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $selected('homeroom_teacher_id', $teacher->id) ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                <p class="text-sm text-muted-foreground">A {{ strtolower(school_term('homeroom_teacher', 'class teacher')) }} can be named later.</p>
            </div>
        </div>
        <p class="text-sm text-muted-foreground">Use the short name staff say out loud, such as “Green”, “A”, or “Blue”. The {{ school_term('class_level', 'class') }} and the {{ school_term('academic_year', 'school year') }} are added around it in every list.</p>
    </div>

    <details class="rounded-md border p-4" {{ $hasDetails ? 'open' : '' }}>
        <summary class="cursor-pointer text-sm font-semibold">3. Optional details</summary>
        <div class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <april:input-group id="label" name="label" label="Local label" value="{{ $value('label') }}" maxlength="255" placeholder="Primary 4 Green" />
                <april:input-group id="stream" name="stream" label="Stream" value="{{ $value('stream') }}" maxlength="100" placeholder="Science" />
                <april:input-group id="shift" name="shift" label="Shift" value="{{ $value('shift') }}" maxlength="100" placeholder="Morning" />
                <april:input-group id="language" name="language" label="Language of instruction" value="{{ $value('language') }}" maxlength="100" />
                <april:input-group id="room" name="room" label="Room" value="{{ $value('room') }}" maxlength="100" />
                <april:input-group id="capacity" name="capacity" type="number" min="1" max="999" label="Capacity" value="{{ $value('capacity') }}" />
                <april:input-group id="position" name="position" type="number" min="0" max="9999" label="Display order" value="{{ $value('position', 0) }}" />
            </div>
        </div>
    </details>

    <div class="flex flex-wrap items-center gap-3">
        <april:button type="submit">{{ $submitLabel }}</april:button>
        <april:button-link href="{{ $cancelHref }}" variant="ghost">Cancel</april:button-link>
    </div>
</form>
