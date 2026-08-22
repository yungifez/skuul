@props(['academicYear', 'model', 'setting' => null, 'isFutureCycle' => true])

@php
    use App\Enums\InstructionalModel;

    $icon = match ($model) {
        InstructionalModel::FixedHomeSections => 'lucide-users',
        InstructionalModel::Hybrid => 'lucide-shuffle',
        InstructionalModel::SubjectBasedSchedule => 'lucide-calendar-clock',
    };
@endphp

<div class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-start gap-4">
        <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
            <x-icon :name="$icon" class="size-5" />
        </div>
        <div class="min-w-0">
            <p class="text-xs font-bold tracking-wider text-muted-foreground uppercase">Teaching now set to</p>
            <p class="mt-1 text-lg font-semibold text-foreground">{{ $model->label() }}</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ $model->description() }}</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 sm:flex-col sm:items-end">
        @if ($setting === null)
            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs text-muted-foreground">
                <x-lucide-circle-dashed class="size-3" />
                Not answered yet
            </span>
            <span class="text-xs text-muted-foreground">The cycle uses the default until somebody answers.</span>
        @else
            <span class="inline-flex items-center gap-1 rounded-full border border-primary/30 px-2.5 py-0.5 text-xs text-foreground">
                <x-lucide-check class="size-3" />
                Answered
            </span>
            <span class="text-xs text-muted-foreground">
                {{ $setting->updatedBy?->name ?? 'A campus administrator' }} · {{ $setting->updated_at?->diffForHumans() }}
            </span>
        @endif

        @if (!$isFutureCycle)
            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs text-muted-foreground">
                <x-lucide-lock class="size-3" />
                Fixed for this cycle
            </span>
        @endif
    </div>
</div>
