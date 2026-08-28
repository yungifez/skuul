@props([
    'action',
    'method' => 'POST',
    'academicLevel' => null,
    'academicLevels',
    'preselectedParentId' => null,
    'submitLabel' => 'Save class',
    'cancelHref',
])

@php
    $value = static fn (string $field, $fallback = null) => old($field, $academicLevel?->{$field} ?? $fallback);
    $selected = static fn (string $field, $option) => (string) old($field, $field === 'parent_id' ? ($academicLevel?->parent_id ?? $preselectedParentId) : $academicLevel?->{$field}) === (string) $option;
    $isGroup = filter_var(old('is_group', $academicLevel?->is_group ?? false), FILTER_VALIDATE_BOOLEAN);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <x-display-validation-errors />

    <april:input-group id="name" name="name" value="{{ $value('name') }}" required maxlength="255" placeholder="Kindergarten, KG 1, or Primary 4">
        <slot:label>
            <span class="inline-flex items-center gap-1">
                Level name
                <x-help-tooltip label="Level name help">The level name is what staff read in lists, such as “Primary 4”. The school-wide label is the word this school uses for a level, such as “Class” or “Form”. It changes wording only and is set once in school setup.</x-help-tooltip>
            </span>
        </slot:label>
    </april:input-group>

    <input type="hidden" name="is_group" value="0">
    <label class="flex cursor-pointer items-start gap-3 rounded-md border border-input p-4 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
        <input type="checkbox" name="is_group" value="1" class="mt-0.5 size-4 shrink-0 rounded border-input" @checked($isGroup)>
        <span class="space-y-1 text-sm">
            <span class="flex items-center gap-1 font-medium">
                This is a level group
                <x-help-tooltip label="Level group help">Use this for an umbrella such as “Kindergarten”. It can contain classes such as “KG 1” and “KG 2”, and a subject can be taught to all of them as one group.</x-help-tooltip>
            </span>
            <span class="block text-muted-foreground">Groups do not have their own sections, but they can receive subjects taught across their child classes.</span>
        </span>
    </label>

    <div class="grid gap-4 md:grid-cols-2">
        <april:input-group id="code" name="code" label="Short code (optional)" value="{{ $value('code') }}" maxlength="100" placeholder="P4" />
        <april:input-group id="position" name="position" type="number" min="0" max="9999" label="Display order" value="{{ $value('position', 0) }}" />
        <div class="flex flex-col gap-2">
            <april:label for="parent">Level group (optional)</april:label>
            <select id="parent" name="parent_id" class="rounded-md border border-input bg-background px-3 py-2">
                <option value="">No parent group. This is a top-level group or standalone level.</option>
                @foreach ($academicLevels as $option)
                    <option value="{{ $option->id }}" {{ $selected('parent_id', $option->id) ? 'selected' : '' }}>{{ $option->name }}</option>
                @endforeach
            </select>
            <p class="text-sm text-muted-foreground">Choose a group when this level belongs under an umbrella such as “Kindergarten”.</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <april:button type="submit">{{ $submitLabel }}</april:button>
        <april:button-link href="{{ $cancelHref }}" variant="ghost">Cancel</april:button-link>
    </div>
</form>
