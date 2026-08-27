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

    <april:input-group id="name" name="name" label="Level name" value="{{ $value('name') }}" required maxlength="255" placeholder="KG 1" />
    <p class="-mt-4 text-sm text-muted-foreground">
        Name the specific level staff place learners into, such as “KG 1” or “Primary 4”. The school’s Class, Grade, Form, or Year wording is set once in school setup.
    </p>

    <div class="grid gap-4 md:grid-cols-2">
        <april:input-group id="code" name="code" label="Short code (optional)" value="{{ $value('code') }}" maxlength="100" placeholder="P4" />
        <april:input-group id="position" name="position" type="number" min="0" max="9999" label="Display order" value="{{ $value('position', 0) }}" />
        <div class="flex flex-col gap-2">
            <april:label for="parent">Level group (optional)</april:label>
            <select id="parent" name="parent_id" class="rounded-md border border-input bg-background px-3 py-2">
                <option value="">None. This is a top-level group.</option>
                @foreach ($academicLevels as $option)
                    <option value="{{ $option->id }}" {{ $selected('parent_id', $option->id) ? 'selected' : '' }}>{{ $option->name }}</option>
                @endforeach
            </select>
            <p class="text-sm text-muted-foreground">Use this for an umbrella such as “Kindergarten”, then place “KG 1” and “KG 2” under it.</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <april:button type="submit">{{ $submitLabel }}</april:button>
        <april:button-link href="{{ $cancelHref }}" variant="ghost">Cancel</april:button-link>
    </div>
</form>
