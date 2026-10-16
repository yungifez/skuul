<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border bg-muted/20 p-3">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="goToCalendarToday" class="inline-flex h-9 items-center rounded-md border bg-background px-3 text-sm font-medium hover:bg-muted">
                Today
            </button>
            <div class="flex items-center gap-1">
                <button type="button" wire:click="previousCalendarPeriod" class="inline-flex size-9 items-center justify-center rounded-md border bg-background text-lg hover:bg-muted" aria-label="Previous period">‹</button>
                <button type="button" wire:click="nextCalendarPeriod" class="inline-flex size-9 items-center justify-center rounded-md border bg-background text-lg hover:bg-muted" aria-label="Next period">›</button>
            </div>
            <span class="ml-1 text-sm font-semibold">{{ $this->calendarHeading() }}</span>
        </div>

        <div class="flex rounded-md border bg-background p-1" role="group" aria-label="Calendar view">
            @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month'] as $view => $label)
                <button type="button" wire:click="setCalendarView('{{ $view }}')" class="rounded px-3 py-1.5 text-sm {{ $calendarView === $view ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted' }}" aria-pressed="{{ $calendarView === $view ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if ($calendarView === 'month')
        <div class="overflow-x-auto beautify-scrollbar">
            <div class="grid min-w-[48rem] grid-cols-7 gap-px overflow-hidden rounded-md border bg-border">
                @foreach (array_keys($weekdayMap) as $weekdayName)
                    <div class="bg-muted/60 px-2 py-2 text-center text-xs font-semibold">{{ $weekdayName }}</div>
                @endforeach

                @foreach ($this->monthWeeks as $week)
                    @foreach ($week as $day)
                        <div wire:key="read-only-month-day-{{ $day['date'] }}" class="min-h-28 bg-background p-2 {{ !$day['in_month'] ? 'bg-muted/20 text-muted-foreground' : '' }} {{ !$day['in_period'] && $day['in_month'] ? 'bg-amber-50/50 dark:bg-amber-950/10' : '' }}">
                            <button type="button" wire:click="chooseCalendarDate('{{ $day['date'] }}')" class="mb-2 flex size-7 items-center justify-center rounded-full text-xs font-semibold hover:bg-primary hover:text-primary-foreground {{ $day['date'] === $calendarDate ? 'bg-primary text-primary-foreground' : '' }}" aria-label="Show {{ $day['date'] }}">{{ $day['day'] }}</button>
                            <div class="min-w-0 space-y-1">
                                @foreach ($day['events'] as $event)
                                    <div wire:key="read-only-month-event-{{ $day['date'] }}-{{ $event['key'] }}" class="min-w-0 max-w-full overflow-hidden rounded border border-primary/20 bg-primary/10 px-1.5 py-1 text-left text-[0.7rem]" title="{{ $event['name'] }} · {{ $event['time'] }}">
                                        <span class="block truncate font-medium">{{ $event['time'] }}</span>
                                        <span class="block truncate">{{ $event['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @elseif ($calendarView === 'week')
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-7">
            @foreach ($this->weekDays as $day)
                <section wire:key="read-only-week-day-{{ $day['date'] }}" class="min-w-0 rounded-lg border {{ !$day['in_period'] ? 'bg-muted/20 opacity-70' : 'bg-background' }}">
                    <button type="button" wire:click="chooseCalendarDate('{{ $day['date'] }}')" class="flex w-full items-center justify-between gap-2 border-b px-3 py-2 text-left hover:bg-muted/50">
                        <span class="text-sm font-semibold"><span class="sm:hidden">{{ $day['short'] }}</span><span class="hidden sm:inline">{{ $day['name'] }}</span></span>
                        <span class="text-xs text-muted-foreground">{{ $day['day'] }}</span>
                    </button>
                    <div class="min-h-28 space-y-2 p-2">
                        @forelse ($day['events'] as $event)
                            <div wire:key="read-only-week-event-{{ $day['date'] }}-{{ $event['key'] }}" class="rounded-md border border-primary/20 bg-primary/10 px-2 py-2 text-xs">
                                <p class="font-medium">{{ $event['name'] }}</p>
                                <p class="mt-1 text-muted-foreground">{{ $event['time'] }}</p>
                                @if ($event['audience_role'])
                                    <p class="mt-1 text-muted-foreground">{{ ucfirst($event['audience_role']) }} only</p>
                                @endif
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-muted-foreground">No events</p>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <section class="rounded-lg border bg-background">
            <div class="border-b px-4 py-3">
                <p class="font-semibold">{{ Illuminate\Support\Carbon::parse($calendarDate)->format('l, j F Y') }}</p>
                <p class="text-sm text-muted-foreground">{{ $this->dayEvents === [] ? 'No events scheduled.' : count($this->dayEvents).' event'.(count($this->dayEvents) === 1 ? '' : 's') }}</p>
            </div>
            <div class="divide-y">
                @forelse ($this->dayEvents as $event)
                    <div wire:key="read-only-day-event-{{ $event['key'] }}" class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $event['name'] }}</p>
                            @if ($event['audience_role'])
                                <p class="text-sm text-muted-foreground">{{ ucfirst($event['audience_role']) }} only</p>
                            @endif
                        </div>
                        <p class="text-sm text-muted-foreground">{{ $event['time'] }}</p>
                    </div>
                @empty
                    <p class="px-4 py-12 text-center text-sm text-muted-foreground">No events scheduled for this date.</p>
                @endforelse
            </div>
        </section>
    @endif
</div>
