@props([
    'icon' => 'lucide-inbox',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-3 px-6 py-12 text-center']) }}>
    <span class="rounded-full border bg-muted/40 p-3 text-muted-foreground">
        <x-icon :name="$icon" class="size-6" />
    </span>
    <p class="text-base font-medium">{{ $title }}</p>
    @if (filled($description))
        <p class="max-w-md text-sm text-muted-foreground">{{ $description }}</p>
    @endif
    @if (trim((string) $slot) !== '')
        <div class="mt-2 flex flex-wrap items-center justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
