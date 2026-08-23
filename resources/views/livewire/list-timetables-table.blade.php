<div>
    <april:card>
        <slot:title>Timetables</slot:title>
        <slot:description>
            A timetable belongs to one home group and one {{ school_term('period', 'period') }}.
            The published one is what the school teaches; a change goes out as the next revision.
        </slot:description>
        <slot:content>
            <div class="space-y-6">
                @unless ($isStudent)
                    <div class="flex max-w-xl flex-col gap-2">
                        <april:label for="academic-cycle-section">Home group</april:label>
                        <april:native-select id="academic-cycle-section" wire:model.live="academicCycleSectionId">
                            @forelse ($cycleSections as $cycleSection)
                                <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                            @empty
                                <option value="">No active home groups in this academic cycle</option>
                            @endforelse
                        </april:native-select>
                    </div>
                @endunless

                @if ($academicCycleSectionId === null)
                    <x-empty-state icon="lucide-users" title="No home group selected"
                        description="Set up an active home group for the current academic cycle before creating a timetable." />
                @elseif ($timetables === [])
                    <x-empty-state icon="lucide-calendar-clock" title="No timetable yet"
                        description="Create a draft for this home group, place its lessons, then publish it when it is ready." />
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
