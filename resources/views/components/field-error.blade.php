@props(['name', 'bag' => 'default'])

{{-- The id lets the control point here with aria-describedby. --}}
@error($name, $bag)
    <p id="{{ field_error_id(field_error_key($name)) }}" {{ $attributes->merge(['class' => 'text-sm text-destructive']) }}>{{ $message }}</p>
@enderror
