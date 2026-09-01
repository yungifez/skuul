@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('calendar-events.index'), 'text' => 'Calendar', 'active'],
]])

@section('title', 'Calendar')
@section('page_heading', 'Calendar')

@section('page_actions')
    @can('create', App\Models\CalendarEvent::class)
        <april:button-link href="{{ route('calendar-events.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Add a day
        </april:button-link>
    @endcan
@endsection

@php
    $byDay = $events->groupBy(fn ($event) => $event->starts_at->toDateString());
    $eventsOn = function ($day) use ($events) {
        return $events->filter(fn ($event) => $event->starts_at->lte($day->copy()->endOfDay())
            && $event->ends_at->gte($day->copy()->startOfDay()));
    };
    $previousMonth = $month->copy()->subMonth()->format('Y-m');
    $nextMonth = $month->copy()->addMonth()->format('Y-m');
    $linkFor = fn (string $value): string => route('calendar-events.index', array_filter([
        'month' => $value,
        'type' => $selectedType?->value,
        'drafts' => $draftsOnly ? 1 : null,
    ]));
    $canWrite = auth()->user()->can('update calendar event');
@endphp

@section('content')
    <div class="space-y-6">
        @if ($canWrite && $draftCount > 0)
            <april:alert>
                <slot:icon><x-lucide-file-pen class="size-4" /></slot:icon>
                <slot:title>{{ $draftCount }} {{ Str::plural('event', $draftCount) }} {{ $draftCount === 1 ? 'is' : 'are' }} still a draft</slot:title>
                <slot:description>
                    A draft is not on the calendar the school reads, and a draft closure does not shut the school.
                    <a class="underline" href="{{ $linkFor($month->format('Y-m')) }}&amp;drafts=1">Show the drafts</a>.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $month->format('F Y') }}</slot:title>
            <slot:description>
                @if ($closures->isEmpty())
                    The school is open every day this month.
                @else
                    The school is shut on
                    {{ $closures->map(fn ($closure) => $closure->starts_at->format('j M'))->join(', ', ' and ') }}.
                @endif
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex gap-2">
                            <april:button-link href="{{ $linkFor($previousMonth) }}" variant="outline" size="sm">
                                <x-lucide-chevron-left class="mr-1 size-4" />
                                {{ $month->copy()->subMonth()->format('M') }}
                            </april:button-link>
                            <april:button-link href="{{ $linkFor(now()->format('Y-m')) }}" variant="outline" size="sm">Today</april:button-link>
                            <april:button-link href="{{ $linkFor($nextMonth) }}" variant="outline" size="sm">
                                {{ $month->copy()->addMonth()->format('M') }}
                                <x-lucide-chevron-right class="ml-1 size-4" />
                            </april:button-link>
                        </div>

                        <form method="GET" action="{{ route('calendar-events.index') }}" class="flex flex-wrap items-end gap-2">
                            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">

                            <div class="flex flex-col gap-2">
                                <april:label for="filter-type" class="sr-only">Kind of day</april:label>
                                <april:native-select id="filter-type" name="type">
                                    <option value="">Every kind</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            @if ($canWrite)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="hidden" name="drafts" value="0">
                                    <input type="checkbox" name="drafts" value="1" @checked($draftsOnly)
                                        class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                                    Drafts only
                                </label>
                            @endif

                            <april:button type="submit" size="sm">
                                <x-lucide-filter class="mr-1 size-4" />
                                Apply
                            </april:button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="grid min-w-[46rem] grid-cols-7 gap-px rounded-lg border bg-border">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                                <div class="bg-muted/50 px-2 py-2 text-center text-xs font-medium text-muted-foreground">{{ $weekday }}</div>
                            @endforeach

                            @foreach ($days as $day)
                                @php $dayEvents = $eventsOn($day); @endphp
                                <div class="min-h-24 bg-background p-2 {{ $day->month === $month->month ? '' : 'opacity-50' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-medium {{ $day->isToday() ? 'rounded-full bg-primary px-1.5 py-0.5 text-primary-foreground' : '' }}">
                                            {{ $day->day }}
                                        </span>
                                        @can('create', App\Models\CalendarEvent::class)
                                            <a href="{{ route('calendar-events.create', ['day' => $day->toDateString()]) }}"
                                                class="text-muted-foreground hover:text-foreground" aria-label="Add a day on {{ $day->format('j F Y') }}">
                                                <x-lucide-plus class="size-3" />
                                            </a>
                                        @endcan
                                    </div>

                                    <ul class="mt-1 space-y-1">
                                        @foreach ($dayEvents as $event)
                                            <li>
                                                <a href="{{ route('calendar-events.edit', $event) }}"
                                                    class="block truncate rounded border px-1.5 py-0.5 text-xs {{ $event->isTeachingDay() ? '' : 'bg-muted font-medium' }} {{ $event->is_published ? '' : 'border-dashed text-muted-foreground' }}"
                                                    title="{{ $event->title }}">
                                                    {{ $event->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What is on this month</slot:title>
            <slot:description>An event with no {{ strtolower(school_term('section', 'section')) }} named is for the whole school.</slot:description>
            <slot:content>
                @if ($events->isEmpty())
                    @if ($selectedType !== null || $draftsOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No day of that kind is in this month.">
                            <april:button-link href="{{ $linkFor($month->format('Y-m')) }}" variant="outline">Show every day</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-calendar-days" title="Nothing is on this month"
                            description="Add holidays, closures, and events so the school and its families read the same calendar.">
                            @can('create', App\Models\CalendarEvent::class)
                                <april:button-link href="{{ route('calendar-events.create') }}">Add the first day</april:button-link>
                            @endcan
                        </x-empty-state>
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Day</april:data-table-head>
                                <april:data-table-head>What is on</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>Who it is for</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($events as $event)
                                <april:data-table-row>
                                    <april:data-table-cell class="whitespace-nowrap font-medium">
                                        {{ $event->starts_at->format('j M') }}
                                        @if ($event->ends_at->toDateString() !== $event->starts_at->toDateString())
                                            to {{ $event->ends_at->format('j M') }}
                                        @endif
                                        <span class="block text-xs text-muted-foreground">
                                            {{ $event->is_all_day ? 'All day' : $event->starts_at->format('H:i').' to '.$event->ends_at->format('H:i') }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        {{ $event->title }}
                                        @if (filled($event->location))
                                            <span class="block text-xs text-muted-foreground">{{ $event->location }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $event->type->label() }}
                                        </span>
                                        @unless ($event->isTeachingDay())
                                            <span class="mt-1 block text-xs text-muted-foreground">The school is shut</span>
                                        @endunless
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        @if ($event->audiences->isEmpty())
                                            The whole school
                                        @else
                                            {{ $event->audiences->map(fn ($audience) => $audience->academicCycleSection?->name ?? $audience->user?->name ?? 'One person')->join(', ') }}
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @unless ($event->is_published)
                                                <span class="whitespace-nowrap rounded-full border border-dashed px-2.5 py-0.5 text-xs text-muted-foreground">Draft</span>
                                            @endunless
                                            <april:button-link href="{{ route('calendar-events.edit', $event) }}" variant="outline" size="sm">
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
            </slot:content>
        </april:card>
    </div>
@endsection
