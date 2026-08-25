@props([
    'action',
    'method' => 'POST',
    'academicLevel' => null,
    'academicLevels',
    'submitLabel' => 'Save class',
    'cancelHref',
])

@php
    $value = static fn (string $field, $fallback = null) => old($field, $academicLevel?->{$field} ?? $fallback);
    $selected = static fn (string $field, $option) => (string) old($field, $academicLevel?->{$field}) === (string) $option;
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <x-display-validation-errors />

    <div class="grid gap-4 md:grid-cols-2">
        <april:input-group id="name" name="name" label="{{ school_term('class_level', 'Class') }} name" value="{{ $value('name') }}" required maxlength="255" />
        <april:input-group id="label" name="label" label="Local label (optional)" value="{{ $value('label') }}" maxlength="255" placeholder="Class, Grade, Form, Year" />
    </div>
    <p class="-mt-4 text-sm text-muted-foreground">
        The {{ strtolower(school_term('class_level', 'class')) }} name is what staff read in lists, such as “Primary 4”. The local label is the word this school uses for a {{ strtolower(school_term('class_level', 'class')) }}, such as “Class” or “Form”. It changes wording only.
    </p>

    <div class="grid gap-4 md:grid-cols-2">
        <april:input-group id="code" name="code" label="Short code (optional)" value="{{ $value('code') }}" maxlength="100" placeholder="P4" />
        <april:input-group id="position" name="position" type="number" min="0" max="9999" label="Display order" value="{{ $value('position', 0) }}" />
        <div class="flex flex-col gap-2">
            <april:label for="parent">Sits under (optional)</april:label>
            <select id="parent" name="parent_id" class="rounded-md border border-input bg-background px-3 py-2">
                <option value="">Nothing. This is a top-level {{ strtolower(school_term('class_level', 'class')) }}.</option>
                @foreach ($academicLevels as $option)
                    <option value="{{ $option->id }}" {{ $selected('parent_id', $option->id) ? 'selected' : '' }}>{{ $option->name }}</option>
                @endforeach
            </select>
            <p class="text-sm text-muted-foreground">Use this to group {{ school_terms('class_level', 'classes') }}, such as putting Primary 4 under Primary.</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <april:button type="submit">{{ $submitLabel }}</april:button>
        <april:button-link href="{{ $cancelHref }}" variant="ghost">Cancel</april:button-link>
    </div>
</form>
