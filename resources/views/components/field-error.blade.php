@props(['name', 'bag' => 'default'])


@error($name, $bag)
    <p id="{{ field_error_id(field_error_key($name)) }}" {{ $attributes->merge(['class' => 'text-sm text-destructive']) }}>{{ $message }}</p>
@enderror
