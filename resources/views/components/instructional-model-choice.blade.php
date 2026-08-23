@props(['option', 'selected' => false, 'readonly' => false, 'impact' => null, 'idPrefix' => 'instructional-model'])

@php
    use App\Enums\InstructionalModel;

    $icon = match ($option) {
        InstructionalModel::FixedHomeSections => 'lucide-users',
        InstructionalModel::Hybrid => 'lucide-shuffle',
        InstructionalModel::SubjectBasedSchedule => 'lucide-calendar-clock',
    };

    $capabilities = [
        ['text' => 'Rosters start as '.strtolower($option->defaultRosterMode()->label()), 'on' => true],
        ['text' => 'Combined class groups', 'on' => $option->allowsCombinedSections()],
        ['text' => 'Named learners', 'on' => $option->allowsIndividualRosters()],
    ];
@endphp

@if ($readonly)
<div class="group flex items-start gap-4 rounded-lg border border-primary/50 bg-primary/5 p-4">
@else
<label for="{{ $idPrefix }}-{{ $option->value }}"
    class="group flex cursor-pointer items-start gap-4 rounded-lg border border-border p-4 transition-colors hover:bg-muted/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
    <input type="radio"
        id="{{ $idPrefix }}-{{ $option->value }}"
        name="model"
        value="{{ $option->value }}"
        class="mt-1 size-4 shrink-0 accent-primary"
        @checked($selected)>
@endif

    <span class="flex min-w-0 flex-1 flex-col gap-2">
        <span class="flex flex-wrap items-center gap-2">
            <span @class([
                'flex size-7 shrink-0 items-center justify-center rounded-full transition-colors',
                'bg-primary/10 text-primary' => $readonly,
                'bg-muted text-muted-foreground group-has-[:checked]:bg-primary/10 group-has-[:checked]:text-primary' => !$readonly,
            ])>
                <x-icon :name="$icon" class="size-4" />
            </span>
            <span class="text-sm font-semibold text-foreground">{{ $option->setupAnswer() }}</span>
            @if ($readonly)
                <span class="inline-flex items-center rounded-full border border-primary/30 px-2 py-0.5 text-[10px] font-medium tracking-wide text-foreground uppercase">The answer in use</span>
            @elseif ($option === InstructionalModel::default())
                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium tracking-wide text-muted-foreground uppercase">Default</span>
            @endif
        </span>

        <span class="block text-sm text-muted-foreground">{{ $option->description() }}</span>

        <span class="block text-xs text-muted-foreground">
            <span class="font-medium text-foreground">Example:</span> {{ $option->example() }}
        </span>

        @if ($impact !== null)
            <span class="flex flex-wrap items-center gap-2 rounded-md bg-muted/50 px-3 py-2 text-xs text-muted-foreground">
                <x-lucide-book-open class="size-3.5 shrink-0" />
                <span>
                    <span class="font-medium text-foreground">{{ $impact['offerings'] }}</span>
                    {{ Str::plural('subject', $impact['offerings']) }} already set up in this cycle.
                    @if ($impact['exceptions'] > 0)
                        <span class="font-medium text-foreground">{{ $impact['exceptions'] }}</span>
                        would keep a roster this answer does not offer, and stay as {{ $impact['exceptions'] === 1 ? 'an exception' : 'exceptions' }}.
                    @else
                        None of them would have to change.
                    @endif
                </span>
            </span>
        @endif

        <span class="flex flex-wrap gap-1.5 pt-0.5">
            @foreach ($capabilities as $capability)
                <span @class([
                    'inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs',
                    'border-primary/30 text-foreground' => $capability['on'],
                    'text-muted-foreground' => !$capability['on'],
                ])>
                    @if ($capability['on'])
                        <x-lucide-check class="size-3" />
                    @else
                        <x-lucide-minus class="size-3" />
                    @endif
                    {{ $capability['text'] }}
                </span>
            @endforeach
        </span>
    </span>
@if ($readonly)
</div>
@else
</label>
@endif
