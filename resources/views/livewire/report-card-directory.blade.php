<div class="space-y-6">
    <april:card>
        <slot:title>Find a card</slot:title>
        <slot:description>Search official cards by learner, academic year, or {{ school_term('period', 'period') }}.</slot:description>
        <slot:content>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div class="flex flex-col gap-2">
                    <april:label for="filter-student">Learner</april:label>
                    <april:native-select id="filter-student" wire:model.live="studentRecordId" class="w-full min-w-0">
                        <option value="">Every learner</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                            </option>
                        @endforeach
                    </april:native-select>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="filter-academic-year">Academic year</april:label>
                    <april:native-select id="filter-academic-year" wire:model.live="academicYearId" class="w-full min-w-0">
                        <option value="">Every academic year</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}">
                                {{ $academicYear->name }}
                            </option>
                        @endforeach
                    </april:native-select>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="filter-period">{{ school_term('period', 'Academic period') }}</april:label>
                    <april:native-select id="filter-period" wire:model.live="academicPeriodId" class="w-full min-w-0">
                        <option value="">Every {{ school_term('period', 'period') }}</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}">
                                {{ $period->academicYear?->name }} · {{ $period->displayName }}
                            </option>
                        @endforeach
                    </april:native-select>
                </div>

                @if ($selectedStudent !== null || $selectedAcademicYear !== null || $selectedPeriod !== null)
                    <april:button type="button" variant="outline" wire:click="clearFilters" wire:loading.attr="disabled">
                        <x-lucide-x class="mr-2 size-4" />
                        Clear filters
                    </april:button>
                @endif
            </div>

            <p wire:loading class="mt-4 text-sm text-muted-foreground" role="status">Updating report cards…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Published cards</slot:title>
        <slot:description>Each version stays exactly as it was issued, even after a later correction.</slot:description>
        <slot:content>
            @if ($reportCards->isEmpty())
                @if ($selectedStudent !== null || $selectedAcademicYear !== null || $selectedPeriod !== null)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                        description="No report card was published for that learner, year, or period.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every card</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-file-text" title="No report cards yet"
                        description="Report cards appear here after a school publishes one." />
                @endif
            @else
                <april:data-table>
                    <slot:header>
                        <april:data-table-row>
                            <april:data-table-head>Learner</april:data-table-head>
                            <april:data-table-head>Academic year</april:data-table-head>
                            <april:data-table-head>{{ school_term('period', 'Period') }}</april:data-table-head>
                            <april:data-table-head>Average</april:data-table-head>
                            <april:data-table-head>Revision</april:data-table-head>
                            <april:data-table-head>Published</april:data-table-head>
                            <april:data-table-head class="text-right">Actions</april:data-table-head>
                        </april:data-table-row>
                    </slot:header>
                    <slot:body>
                        @foreach ($reportCards as $card)
                            <april:data-table-row>
                                <april:data-table-cell class="font-medium">
                                    {{ $card->studentRecord->user?->name ?? $card->studentRecord->admission_number }}
                                    <span class="block text-xs text-muted-foreground">{{ $card->studentRecord->admission_number }}</span>
                                </april:data-table-cell>
                                <april:data-table-cell>{{ $card->academicYear?->name ?? 'Unknown year' }}</april:data-table-cell>
                                <april:data-table-cell>{{ $card->academicPeriod->label ?? $card->academicPeriod->name }}</april:data-table-cell>
                                <april:data-table-cell>
                                    {{ $card->average_percentage === null ? '—' : number_format($card->average_percentage, 2).'%' }}
                                </april:data-table-cell>
                                <april:data-table-cell>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                        Revision {{ $card->revision }}
                                    </span>
                                </april:data-table-cell>
                                <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                    {{ $card->published_at->format('j M Y') }}
                                </april:data-table-cell>
                                <april:data-table-cell class="text-right">
                                    <april:button-link href="{{ route('report-cards.show', $card) }}" variant="outline" size="sm"
                                        aria-label="View the report card for {{ $card->studentRecord->user?->name ?? $card->studentRecord->admission_number }}">
                                        <x-lucide-eye class="mr-1 size-4" />
                                        View
                                    </april:button-link>
                                </april:data-table-cell>
                            </april:data-table-row>
                        @endforeach
                    </slot:body>
                </april:data-table>

                <div class="pt-4">
                    {{ $reportCards->links('components.datatable-pagination-links-view') }}
                </div>
            @endif
        </slot:content>
    </april:card>
</div>
