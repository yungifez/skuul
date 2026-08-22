@props([
    'href',
    'ability' => null,
    'arguments' => [],
])

@if ($ability === null || auth()->user()?->can($ability, $arguments))
    <span data-resource-create-action="{{ $href }}">
        <april:button-link href="{{ $href }}" class="gap-1.5">
            <x-lucide-plus class="size-4" />
            {{ $slot }}
        </april:button-link>
    </span>
@endif
