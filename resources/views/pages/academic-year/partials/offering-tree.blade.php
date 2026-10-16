@foreach ($offerings->groupBy('subject_id') as $subjectOfferings)
    @php
        $subject = $subjectOfferings->first()->subject;
        $inactiveStatuses = $subjectOfferings
            ->map(fn ($offering) => $offering->status)
            ->reject(fn ($status): bool => $status->value === 'active')
            ->unique(fn ($status) => $status->value)
            ->values();
        $periodsByLearners = $subjectOfferings
            ->groupBy('academic_period_id')
            ->map(fn ($periodOfferings): array => [
                'period' => $periodOfferings->first()->academicPeriod,
                'offering' => $periodOfferings->first(),
                'learners' => $periodOfferings
                    ->map(fn ($offering): string => $offering->roster_mode->usesHomeSections()
                        ? $offering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', ')
                        : school_roster_label($offering->roster_mode))
                    ->unique()
                    ->join(', '),
            ])
            ->groupBy('learners');
    @endphp
    <div wire:key="course-offering-subject-{{ $subject->id }}-{{ $subjectOfferings->pluck('id')->join('-') }}" class="flex min-h-11 min-w-0 flex-wrap items-center gap-x-3 gap-y-0.5 py-1 text-sm">
        <span class="flex min-w-0 items-center gap-2">
            <x-lucide-book-open class="size-4 shrink-0 text-muted-foreground" />
            <span class="font-medium">{{ $subject->name }}</span>
            @if ($subject->short_name)
                <span class="text-xs text-muted-foreground">{{ $subject->short_name }}</span>
            @endif
        </span>
        @foreach ($periodsByLearners as $learners => $periods)
            <span class="flex min-w-0 flex-wrap items-center gap-x-1 text-muted-foreground">
                @foreach ($periods as $entry)
                    @can('update', $entry['offering'])
                        <a href="{{ route('course-offerings.edit', [$entry['offering'], 'setup' => 1]) }}" class="text-foreground hover:underline" title="Edit {{ $entry['period']?->displayName }} for {{ $learners }}">{{ $entry['period']?->displayName }}</a>
                    @else
                        <span class="text-foreground">{{ $entry['period']?->displayName }}</span>
                    @endcan
                    @unless ($loop->last)
                        <span>,</span>
                    @endunless
                @endforeach
                <span>· {{ $learners }}</span>
            </span>
        @endforeach
        @foreach ($inactiveStatuses as $status)
            <april:badge variant="secondary">{{ $status->label() }}</april:badge>
        @endforeach
    </div>
@endforeach
