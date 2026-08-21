@props([
    'name',
    'label' => null,
    'id' => null,
    'errorBag' => 'default',
])

@php
    $fieldId = $id ?? $name;
    $errorMessage = $errors->getBag($errorBag)->first($name);
    $selectAttributes = $attributes->except(['id', 'name'])->merge([
        'aria-invalid' => $errorMessage ? 'true' : null,
        'aria-describedby' => $errorMessage ? $fieldId . '-error' : null,
    ]);
@endphp

<div class="grid gap-2">
    @if ($label !== null)
        <april:label for="{{ $fieldId }}">{{ $label }}</april:label>
    @endif

    <april:native-select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        :attributes="$selectAttributes"
    >
        {{ $slot }}
    </april:native-select>

    @if ($errorMessage)
        <p id="{{ $fieldId }}-error" class="text-sm text-destructive">{{ $errorMessage }}</p>
    @endif
</div>
