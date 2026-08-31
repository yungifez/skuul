{{--
    One week of a timetable: a column for each weekday, a row for each time
    slot. The read-only grid, the builder, and the printed sheet all draw
    this, so a change to the week reaches every screen at once.

    It stays a real table on purpose. The browser print view can paginate it
    cleanly while preserving the same layout as the screen.

    $grid       the array App\Services\Timetable\TimetableGrid returns
    $editable   whether clicking a cell selects it for the builder
    $selected   "slotId:weekdayId" of the cell being worked on, or null
--}}
@php
    $editable = $editable ?? false;
    $selected = $selected ?? null;
    // A weekend column appears only for a school that teaches then.
    $weekdays = array_values(array_filter(
        $grid['weekdays'],
        fn (array $weekday): bool => $editable || !$weekday['is_weekend'] || $weekday['used'],
    ));
@endphp

@if ($grid['rows'] === [])
    <x-empty-state icon="lucide-clock" title="No time slots yet"
        description="A timetable is a week of time slots. Add the first one to start placing lessons." />
@else
    <div class="overflow-x-auto beautify-scrollbar">
        <table class="w-full min-w-[44rem] border-separate border-spacing-1 text-sm">
            <thead>
                <tr>
                    <th scope="col" class="w-28 px-2 py-2 text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        Time
                    </th>
                    @foreach ($weekdays as $weekday)
                        <th scope="col" class="rounded-md bg-muted/60 px-2 py-2 text-center text-xs font-semibold">
                            <span class="hidden sm:inline">{{ $weekday['name'] }}</span>
                            <span class="sm:hidden">{{ $weekday['short'] }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($grid['rows'] as $row)
                    <tr>
                        <th scope="row" class="whitespace-nowrap rounded-md bg-muted/60 px-2 py-2 text-left align-middle text-xs font-medium">
                            {{ $row['start'] }}
                            <span class="block font-normal text-muted-foreground">{{ $row['stop'] }}</span>
                        </th>

                        @foreach ($weekdays as $weekday)
                            @php
                                $cell = $row['cells'][$weekday['id']];
                                $key = $row['id'].':'.$weekday['id'];
                                $isActive = $cell['active'] ?? true;
                                $isSelected = $editable && $isActive && $selected === $key;
                                $tone = match ($cell['kind']) {
                                    'subject' => 'bg-primary/10 border-primary/30',
                                    'break' => 'bg-muted border-dashed',
                                    default => 'border-dashed',
                                };
                            @endphp

                            <td class="p-0 align-top">
                                @if ($editable && $isActive)
                                    <button type="button"
                                        wire:click="selectCell({{ $row['id'] }}, {{ $weekday['id'] }})"
                                        wire:loading.attr="disabled"
                                        class="h-full min-h-16 w-full rounded-md border px-2 py-2 text-left transition hover:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-ring {{ $tone }} {{ $isSelected ? 'ring-2 ring-ring' : '' }}"
                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        aria-label="{{ $weekday['name'] }} at {{ $row['start'] }}: {{ $cell['name'] ?? 'empty' }}">
                                        @include('livewire.partials.timetable-cell', ['cell' => $cell])
                                    </button>
                                @else
                                    <div class="min-h-16 rounded-md border px-2 py-2 {{ $tone }} {{ !$isActive ? 'bg-muted/20 opacity-50' : '' }}">
                                        @include('livewire.partials.timetable-cell', ['cell' => $cell])
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
