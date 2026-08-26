@props(['academicYear'])

@php
    use App\Enums\InstructionalModel;
    use App\Services\Curriculum\InstructionalModelResolver;

    $resolver = app(InstructionalModelResolver::class);
    $resolver->preloadForSchool($academicYear->school_id);
    $setting = $resolver->settingFor($academicYear);
    $model = $setting?->model ?? InstructionalModel::default();

    $icon = match ($model) {
        InstructionalModel::FixedHomeSections => 'lucide-users',
        InstructionalModel::Hybrid => 'lucide-shuffle',
        InstructionalModel::SubjectBasedSchedule => 'lucide-calendar-clock',
    };
@endphp

<a href="{{ route('academic-years.instructional-model.edit', $academicYear->id) }}"
    @class([
        'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs transition-colors hover:bg-accent',
        'border-primary/30 text-foreground' => $setting !== null,
        'text-muted-foreground' => $setting === null,
    ])
    title="{{ school_instructional_model_description($model) }}">
    <x-icon :name="$icon" class="size-3.5" />
    {{ $model->label() }}
    @if ($setting === null)
        <span class="text-[10px] tracking-wide uppercase">default</span>
    @endif
</a>
