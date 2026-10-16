@props([
    'checklist',
])

@php
    $progress = $checklist['total'] > 0
        ? round(($checklist['completed'] / $checklist['total']) * 100)
        : 100;
    $groups = collect($checklist['items'])->groupBy('group');
@endphp

<section class="flex flex-col gap-6" aria-labelledby="school-setup-checklist-heading">
    <div class="flex flex-col gap-3">
        <div class="flex items-baseline justify-between gap-4">
            <h2 id="school-setup-checklist-heading" class="text-lg font-semibold">School setup checklist</h2>
            <p class="shrink-0 text-sm text-muted-foreground"><span class="font-semibold text-foreground">{{ $checklist['completed'] }}</span> of {{ $checklist['total'] }} done</p>
        </div>
        <div class="h-1.5 overflow-hidden rounded-full bg-muted" role="progressbar" aria-label="School setup progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}">
            <div class="h-full rounded-full bg-foreground/70" style="width: {{ $progress }}%"></div>
        </div>
        @if ($checklist['required_remaining'] > 0)
            <p class="text-sm text-muted-foreground">{{ $checklist['required_remaining'] }} required {{ $checklist['required_remaining'] === 1 ? 'step remains' : 'steps remain' }}</p>
        @endif
    </div>

    @foreach ($groups as $group => $items)
        <div class="flex flex-col gap-2">
            <h3 class="text-sm font-medium text-muted-foreground">{{ $group }}</h3>
            <ul class="divide-y border-y">
                @foreach ($items as $item)
                    <li>
                        <a href="{{ $item['url'] }}" class="group flex min-h-11 items-center gap-3 py-3 select-none">
                            @if ($item['complete'])
                                <x-lucide-circle-check class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" aria-hidden="true" />
                            @else
                                <span class="size-5 shrink-0 rounded-full border-2 border-muted-foreground/40" aria-hidden="true"></span>
                            @endif
                            <span class="flex min-w-0 flex-1 flex-col">
                                <span class="text-sm font-medium group-hover:underline">{{ $item['title'] }}<span class="sr-only">{{ $item['complete'] ? ', done' : ', to do' }}</span></span>
                                @if (!$item['complete'])
                                    <span class="text-sm text-muted-foreground">{{ $item['reason'] }}</span>
                                @endif
                            </span>
                            <x-lucide-chevron-right class="size-4 shrink-0 text-muted-foreground" aria-hidden="true" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</section>
