@props([
    'name',
    'label' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'autocomplete' => null,
    'errorBag' => 'default',
])

@php
    $fieldId = $id ?? $name;
    $errorMessage = $errors->getBag($errorBag)->first($name);
    $fieldValue = old($name, $value);
    $inputAttributes = $attributes->except(['id', 'name', 'type', 'value', 'autocomplete'])->merge([
        'autocomplete' => $autocomplete,
        'aria-invalid' => $errorMessage ? 'true' : null,
        'aria-describedby' => $errorMessage ? $fieldId . '-error' : null,
    ]);

    if ($type === 'password') {
        $inputAttributes = $inputAttributes->merge([
            'x-bind:type' => "showPassword ? 'text' : 'password'",
        ]);
    }
@endphp

<div class="grid gap-2">
    @if ($label !== null)
        <april:label for="{{ $fieldId }}">{{ $label }}</april:label>
    @endif

    @if ($type === 'password')
        <div x-data="{ showPassword: false }" class="relative">
            <april:input
                id="{{ $fieldId }}"
                name="{{ $name }}"
                type="password"
                value="{{ $fieldValue }}"
                class="w-full pr-16"
                :attributes="$inputAttributes"
            />
            <button
                type="button"
                class="absolute inset-y-0 right-3 inline-flex items-center text-xs font-medium text-muted-foreground hover:text-foreground"
                x-on:click="showPassword = ! showPassword"
                x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
                <span x-text="showPassword ? 'Hide' : 'Show'"></span>
            </button>
        </div>
    @else
        <april:input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $fieldValue }}"
            class="w-full"
            :attributes="$inputAttributes"
        />
    @endif

    @if ($errorMessage)
        <p id="{{ $fieldId }}-error" class="text-sm text-destructive">{{ $errorMessage }}</p>
    @endif
</div>
