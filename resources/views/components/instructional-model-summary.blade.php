@props(['academicYear'])

@php
    use App\Enums\InstructionalModel;
    use App\Actions\Curriculum\SetInstructionalModel;
    use App\Services\Curriculum\InstructionalModelResolver;

    $setting = app(InstructionalModelResolver::class)->settingFor($academicYear);
    $model = $setting?->model ?? InstructionalModel::default();
    $isFutureCycle = app(SetInstructionalModel::class)->isFutureCycle($academicYear);

    $icon = match ($model) {
        InstructionalModel::FixedHomeSections => 'lucide-users',
        InstructionalModel::Hybrid => 'lucide-shuffle',
        InstructionalModel::SubjectBasedSchedule => 'lucide-calendar-clock',
    };
@endphp

<div class="group flex flex-col justify-between rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm transition-all duration-300 hover:border-border">
    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-start justify-between gap-3">
            <p class="text-xs font-bold text-muted-foreground uppercase">Teaching setup</p>

            @if (!$isFutureCycle)
                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs text-muted-foreground">
                    <x-lucide-lock class="size-3" />
                    Fixed for this {{ strtolower(school_term('academic_year', 'school year')) }}
                </span>
            @elseif ($setting === null)
                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs text-muted-foreground">
                    <x-lucide-circle-dashed class="size-3" />
                    Not answered yet
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full border border-primary/30 px-2.5 py-0.5 text-xs text-foreground">
                    <x-lucide-check class="size-3" />
                    Answered
                </span>
            @endif
        </div>

        <div class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                <x-icon :name="$icon" class="size-5" />
            </div>
            <div class="min-w-0">
                <h3 class="text-xl font-bold text-foreground">{{ $model->label() }}</h3>
                <p class="mt-1 text-sm text-muted-foreground">{{ school_instructional_model_description($model) }}</p>
            </div>
        </div>

        <p class="text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ InstructionalModel::SETUP_QUESTION }}</span>
            Answer it once, and every subject in this {{ strtolower(school_term('academic_year', 'school year')) }} starts the same way.
        </p>
    </div>

    <div class="flex justify-end border-t border-sidebar-border/50 p-4">
        <a href="{{ route('academic-years.instructional-model.edit', $academicYear->id) }}"
            class="inline-flex h-9 items-center justify-center gap-1 rounded-md px-3 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground">
            Open teaching setup
            <x-lucide-arrow-right class="ml-1 size-3.5 transition-transform group-hover:translate-x-1" />
        </a>
    </div>
</div>
