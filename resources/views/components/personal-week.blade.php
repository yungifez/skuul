{{-- One person's week put together from several timetables, a list of lessons for each day.
    $days: PersonalWeek::of() output. $headingLevel: the level of the day headings. --}}
@props(['days', 'headingLevel' => 3])

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($days as $day)
        <section class="flex flex-col gap-2" aria-labelledby="week-day-{{ $loop->index }}">
            <h{{ $headingLevel }} id="week-day-{{ $loop->index }}" class="text-sm font-semibold">{{ $day['weekday'] }}</h{{ $headingLevel }}>
            <ul class="divide-y rounded-md border">
                @foreach ($day['lessons'] as $lesson)
                    <li class="flex gap-3 px-3 py-2 text-sm">
                        <span class="w-24 shrink-0 tabular-nums text-muted-foreground">{{ $lesson['start'] }}–{{ $lesson['stop'] }}</span>
                        <span class="min-w-0">
                            <span class="block font-medium">{{ $lesson['name'] }}</span>
                            <span class="block text-xs text-muted-foreground">
                                @if ($lesson['teachers'] !== []){{ implode(', ', $lesson['teachers']) }} · @endif{{ $lesson['timetable'] }}
                            </span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
</div>
