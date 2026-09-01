@foreach ($offerings->groupBy('subject_id') as $subjectOfferings)
    @php
        $subject = $subjectOfferings->first()->subject;
        $offeringsByPeriod = $subjectOfferings->groupBy('academic_period_id');
        $statuses = $subjectOfferings
            ->map(fn ($offering) => $offering->status)
            ->unique(fn ($status) => $status->value)
            ->values();
        $periodCount = $offeringsByPeriod->count();
        $periodLabel = $periodCount === 1 ? '1 period' : $periodCount.' periods';
    @endphp
    <div wire:key="course-offering-subject-{{ $subject->id }}-{{ $subjectOfferings->pluck('id')->join('-') }}" class="flex min-w-0 flex-col gap-2 rounded-md border border-primary/20 bg-primary/5 px-3 py-2 text-sm sm:flex-row sm:items-start sm:justify-between">
        <div class="flex min-w-0 items-start gap-2">
            <x-lucide-book-marked class="mt-0.5 size-4 shrink-0 text-primary-foreground" />
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="font-medium">{{ $subject->name }}</span>
                    @if ($subject->short_name)
                        <span class="text-xs text-muted-foreground">{{ $subject->short_name }}</span>
                    @endif
                </div>
                <p class="text-xs text-muted-foreground">Offered in {{ $periodLabel }}:</p>
                <div class="mt-1 flex flex-wrap gap-1.5">
                    @foreach ($offeringsByPeriod as $periodOfferings)
                        @php
                            $period = $periodOfferings->first()->academicPeriod;
                        @endphp
                        <div class="inline-flex flex-wrap items-center gap-1 rounded-md border bg-background px-2 py-1 text-xs">
                            <span class="font-medium">{{ $period?->displayName }}</span>
                            <span class="text-muted-foreground">·</span>
                            @foreach ($periodOfferings as $periodOffering)
                                @php
                                    $offeringSections = $periodOffering->roster_mode->usesHomeSections()
                                        ? $periodOffering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', ')
                                        : school_roster_label($periodOffering->roster_mode);
                                @endphp
                                @can('update', $periodOffering)
                                    <a href="{{ route('course-offerings.edit', [$periodOffering, 'setup' => 1]) }}" class="font-medium text-primary-foreground hover:underline" title="Edit {{ $period?->displayName }} for {{ $offeringSections }}">{{ $offeringSections }}</a>
                                @else
                                    <span>{{ $offeringSections }}</span>
                                @endcan
                                @unless ($loop->last)
                                    <span class="text-muted-foreground">,</span>
                                @endunless
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex w-full shrink-0 items-center justify-end gap-2 border-t pt-2 sm:w-auto sm:justify-start sm:border-t-0 sm:pt-0">
            @foreach ($statuses as $status)
                <april:badge variant="{{ $status->value === 'active' ? 'default' : 'secondary' }}">{{ $status->label() }}</april:badge>
            @endforeach
        </div>
    </div>
@endforeach
