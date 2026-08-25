@props([
    'label' => 'More information',
])

<april:tooltip x-teleport="body">
    <slot:trigger>
        <button type="button" aria-label="{{ $label }}" class="inline-flex size-7 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
            <x-lucide-circle-help class="size-4" />
        </button>
    </slot:trigger>
    <slot:content>
        <div class="max-w-xs leading-5">{{ $slot }}</div>
    </slot:content>
</april:tooltip>
