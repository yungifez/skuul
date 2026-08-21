@php
    $inputAttributes = $attributes->merge(['class' => $class]);

    if ($value !== null) {
        $inputAttributes = $inputAttributes->merge(['value' => $value]);
    }
@endphp

<div class="{{$groupClass}}">
    <april:input-group
        name="{{$name}}"
        label="{{$label}}"
        :error-bag="$errorBag"
        id="{{$id}}"
        :attributes="$inputAttributes"
    />
</div>
