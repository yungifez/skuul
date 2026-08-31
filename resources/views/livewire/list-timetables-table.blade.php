<div>
    <april:card>
        <slot:title>Timetables</slot:title>
        <slot:description>
            A timetable repeats weekly during one {{ strtolower(school_term('period', 'period')) }}. It can belong to a {{ strtolower(school_term('section', 'section')) }} or be schoolwide.
            The published one is what the school teaches; a change goes out as the next revision.
        </slot:description>
        <slot:content>
            <div class="space-y-6">
                @unless ($isStudent)
                    <div class="grid max-w-xl gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <april:label for="timetable-scope">Schedule for</april:label>
                            <april:native-select id="timetable-scope" wire:model.live="scope">
                                <option value="section">A section</option>
                                <option value="schoolwide">Schoolwide</option>
                            </april:native-select>
                        </div>
                        @if ($scope === 'section')
                        <div class="flex flex-col gap-2">
                        <april:label for="academic-cycle-section">{{ school_term('section', 'Section') }}</april:label>
                        <april:native-select id="academic-cycle-section" wire:model.live="academicCycleSectionId">
                            @forelse ($cycleSections as $cycleSection)
                                <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                            @empty
                                <option value="">No active {{ strtolower(school_terms('section', 'sections')) }} in this {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                            @endforelse
                        </april:native-select>
                        </div>
                        @endif
                    </div>
                @endunless

                @if ($scope === 'section' && $academicCycleSectionId === null)
                    <x-empty-state icon="lucide-users" title="No {{ strtolower(school_term('section', 'section')) }} selected"
                        description="Set up an active {{ strtolower(school_term('section', 'section')) }} for the current {{ strtolower(school_term('academic_year', 'school year')) }} before creating a timetable." />
                @elseif ($timetables === [])
                    <x-empty-state icon="lucide-calendar-clock" title="No timetable yet"
                        description="Create a draft for this {{ strtolower(school_term('section', 'section')) }}, place its lessons, then publish it when it is ready." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Timetable</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head>Published</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($timetables as $timetable)
                                <april:data-table-row>
                                    <april:data-table-cell>
                                        <span class="font-medium">{{ $timetable['name'] }}</span>
                                        <span class="block text-xs text-muted-foreground">
                                            Revision {{ $timetable['revision'] }}@if ($timetable['description']) · {{ $timetable['description'] }}@endif
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span wire:key="state-{{ $timetable['id'] }}">
                                            <april:badge variant="{{ $timetable['variant'] }}">{{ $timetable['status'] }}</april:badge>
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $timetable['published_at'] ?? 'Not published' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($timetable['can_manage'])
                                                <april:button-link href="{{ route('timetables.manage', $timetable['id']) }}" variant="outline" size="sm">
                                                    <x-lucide-pencil-ruler class="mr-1 size-4" />
                                                    Build
                                                </april:button-link>
                                            @endif
                                            <april:button-link href="{{ route('timetables.show', $timetable['id']) }}" variant="outline" size="sm">
                                                <x-lucide-eye class="mr-1 size-4" />
                                                Open
                                            </april:button-link>
                                        </div>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </div>
        </slot:content>
    </april:card>
</div>
