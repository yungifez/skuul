@props(['label' => 'Continue', 'type' => 'submit'])

<april:button type="{{ $type }}" x-bind:disabled="submitting" x-bind:aria-busy="submitting" {{ $attributes->
    class(['justify-center']) }}
    >
    <span x-show="! submitting">{{ $slot->isEmpty() ? $label : $slot }}</span>
    <span x-show="submitting" x-cloak>Working...</span>
</april:button>
