@props(['option', 'selected' => false, 'readonly' => false, 'impact' => null, 'idPrefix' => 'instructional-model'])

@php
    use App\Enums\InstructionalModel;

    $icon = match ($option) {
        InstructionalModel::FixedHomeSections => 'lucide-users',
        InstructionalModel::Hybrid => 'lucide-shuffle',
        InstructionalModel::SubjectBasedSchedule => 'lucide-calendar-clock',
    };

    $capabilities = [
        ['text' => 'Subjects start with '.strtolower(school_roster_label($option->defaultRosterMode())), 'on' => true],
        ['text' => 'Combined '.strtolower(school_terms('section', 'sections')), 'on' => $option->allowsCombinedSections()],
        ['text' => 'Named learners', 'on' => $option->allowsIndividualRosters()],
    ];
@endphp

<div @class([
    'group relative flex items-start gap-4 rounded-lg p-4',
    'border border-primary/50 bg-primary/5' => $readonly,
    'border border-border transition-colors hover:bg-muted/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5' => !$readonly,
])>
    @if ($readonly)
        <div class="flex min-w-0 flex-1 items-start gap-4">
    @else
        <label for="{{ $idPrefix }}-{{ $option->value }}" class="flex min-w-0 flex-1 cursor-pointer items-start gap-4 pr-8">
            <input type="radio"
                id="{{ $idPrefix }}-{{ $option->value }}"
                name="model"
                value="{{ $option->value }}"
                class="mt-1 size-4 shrink-0 accent-primary"
                @checked($selected)>
    @endif

        <span @class([
            'flex size-7 shrink-0 items-center justify-center rounded-full transition-colors',
            'bg-primary/10 text-primary' => $readonly,
            'bg-muted text-muted-foreground group-has-[:checked]:bg-primary/10 group-has-[:checked]:text-primary' => !$readonly,
        ])>
            <x-icon :name="$icon" class="size-4" />
        </span>

        <span class="flex min-w-0 flex-1 flex-col gap-2">
            <span class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-foreground">{{ $option->setupAnswer() }}</span>
                @if ($readonly)
                    <span class="inline-flex items-center rounded-full border border-primary/30 px-2 py-0.5 text-[10px] font-medium tracking-wide text-foreground uppercase">The answer in use</span>
                @elseif ($option === InstructionalModel::default())
                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium tracking-wide text-muted-foreground uppercase">Default</span>
                @endif
            </span>

            @if ($impact !== null)
                <span class="flex items-center gap-2 text-xs text-muted-foreground">
                    <x-lucide-book-open class="size-3.5 shrink-0" />
                    {{ $impact['offerings'] }} {{ Str::plural('subject', $impact['offerings']) }} already set up
                    @if ($impact['exceptions'] > 0)
                        · {{ $impact['exceptions'] }} {{ Str::plural('exception', $impact['exceptions']) }}
                    @endif
                </span>
            @endif
        </span>
    @if ($readonly)
        </div>
    @else
        </label>
    @endif

    <div class="absolute top-3 right-3">
        <x-help-tooltip :label="$option->setupAnswer().' details'">
            <p>{{ school_instructional_model_description($option) }}</p>
            <p class="mt-2"><span class="font-medium">Example:</span> {{ $option->example() }}</p>
            <ul class="mt-2 flex flex-col gap-1">
                @foreach ($capabilities as $capability)
                    <li class="flex items-start gap-1.5">
                        @if ($capability['on'])
                            <x-lucide-check class="mt-0.5 size-3.5 shrink-0" />
                        @else
                            <x-lucide-minus class="mt-0.5 size-3.5 shrink-0" />
                        @endif
                        {{ $capability['text'] }}
                    </li>
                @endforeach
            </ul>
            @if ($impact !== null)
                <p class="mt-2 border-t border-border pt-2">
                    <span class="font-medium">Impact:</span>
                    {{ $impact['offerings'] }} {{ Str::plural('subject', $impact['offerings']) }} already set up in this {{ strtolower(school_term('academic_year', 'school year')) }}.
                    @if ($impact['exceptions'] > 0)
                        {{ $impact['exceptions'] }} would keep a roster this answer does not offer and remain as {{ Str::plural('an exception', $impact['exceptions']) }}.
                    @else
                        None of them would have to change.
                    @endif
                </p>
            @endif
        </x-help-tooltip>
    </div>
</div>
