@props([
    'checklist',
])

@php
    $progress = $checklist['total'] > 0
        ? round(($checklist['completed'] / $checklist['total']) * 100)
        : 100;
    $groups = collect($checklist['items'])->groupBy('group');
@endphp

<section class="rounded-xl border bg-card p-6 md:p-8" aria-labelledby="school-setup-checklist-heading">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <div class="flex items-center gap-2">
                <h2 id="school-setup-checklist-heading" class="text-xl font-semibold tracking-tight">School setup checklist</h2>
                <x-help-tooltip label="School setup checklist help">These steps show what your school needs before staff can use the academic workspace. The list updates as you complete each area.</x-help-tooltip>
            </div>
            <p class="mt-1 text-sm text-muted-foreground">Set up the essentials first, then add the recommended records your team will use every day.</p>
        </div>
        <div class="shrink-0 text-left sm:text-right">
            <p class="text-2xl font-semibold tracking-tight">{{ $checklist['completed'] }} <span class="text-base font-normal text-muted-foreground">of {{ $checklist['total'] }}</span></p>
            <p class="text-xs text-muted-foreground">steps complete</p>
        </div>
    </div>

    <div class="mt-5 h-2 overflow-hidden rounded-full bg-muted" role="progressbar" aria-label="School setup progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}">
        <div class="h-full rounded-full bg-primary transition-all" style="width: {{ $progress }}%"></div>
    </div>

    @if ($checklist['required_remaining'] > 0)
        <div class="mt-5 flex items-start gap-3 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 text-sm">
            <x-lucide-triangle-alert class="mt-0.5 size-4 shrink-0 text-amber-700 dark:text-amber-300" />
            <p><span class="font-semibold">{{ $checklist['required_remaining'] }} required {{ $checklist['required_remaining'] === 1 ? 'step remains' : 'steps remain' }}.</span> Complete these before your team starts working in this {{ strtolower(school_term('academic_year', 'school year')) }}.</p>
        </div>
    @else
        <div class="mt-5 flex items-start gap-3 rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm">
            <x-lucide-circle-check class="mt-0.5 size-4 shrink-0 text-emerald-700 dark:text-emerald-300" />
            <p><span class="font-semibold">The required setup is complete.</span> You can continue with the recommended steps below.</p>
        </div>
    @endif

    <div class="mt-6 space-y-7">
        @foreach ($groups as $group => $items)
            <div>
                <h3 class="text-sm font-semibold">{{ $group }}</h3>
                <div class="mt-3 divide-y rounded-lg border">
                    @foreach ($items as $item)
                        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 items-start gap-3">
                                @if ($item['complete'])
                                    <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                        <x-lucide-check class="size-3.5" />
                                    </span>
                                @else
                                    <span class="mt-0.5 size-5 shrink-0 rounded-full border-2 border-muted-foreground/40"></span>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1">
                                        <h4 class="font-medium">{{ $item['title'] }}</h4>
                                        <x-help-tooltip :label="$item['title'].' help'">{{ $item['description'] }}</x-help-tooltip>
                                    </div>
                                    @if ($item['complete'])
                                        <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300">Complete</p>
                                    @else
                                        <p class="mt-1 text-sm text-muted-foreground">{{ $item['reason'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @if (!$item['complete'])
                                <a href="{{ $item['url'] }}" class="inline-flex shrink-0 items-center justify-center rounded-md border px-3 py-2 text-sm font-medium transition-colors hover:bg-muted focus:outline-none focus:ring-2 focus:ring-ring sm:mt-0">
                                    {{ $item['action'] }}
                                    <span aria-hidden="true" class="ml-2">→</span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
