@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'School calendar', 'active'],
]])

@section('title', 'School calendar')
@section('page_heading', 'School calendar')

@php
    $eventsOn = fn ($day) => $events->filter(fn ($event) => $event->starts_at->lte($day->copy()->endOfDay())
        && $event->ends_at->gte($day->copy()->startOfDay()));
    $linkFor = fn (string $value): string => route('portal.calendar.index', [$studentRecord, 'month' => $value]);
@endphp

@section('content')
    <div class="space-y-6">
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
                            <april:button-link href="{{ $linkFor($month->copy()->subMonth()->format('Y-m')) }}" variant="outline" size="sm">
                                <x-lucide-chevron-left class="mr-1 size-4" />
                                {{ $month->copy()->subMonth()->format('M') }}
                            </april:button-link>
                            <april:button-link href="{{ $linkFor(now()->format('Y-m')) }}" variant="outline" size="sm">Today</april:button-link>
                            <april:button-link href="{{ $linkFor($month->copy()->addMonth()->format('Y-m')) }}" variant="outline" size="sm">
                                {{ $month->copy()->addMonth()->format('M') }}
                                <x-lucide-chevron-right class="ml-1 size-4" />
                            </april:button-link>
                        </div>

                        <p class="text-sm text-muted-foreground">
                            {{ $studentRecord->user?->name ?? $studentRecord->admission_number }}
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="grid min-w-[46rem] grid-cols-7 gap-px rounded-lg border bg-border">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                                <div class="bg-muted/50 px-2 py-2 text-center text-xs font-medium text-muted-foreground">{{ $weekday }}</div>
                            @endforeach

                            @foreach ($days as $day)
                                <div class="min-h-24 bg-background p-2 {{ $day->month === $month->month ? '' : 'opacity-50' }}">
                                    <span class="text-xs font-medium {{ $day->isToday() ? 'rounded-full bg-primary px-1.5 py-0.5 text-primary-foreground' : '' }}">
                                        {{ $day->day }}
                                    </span>

                                    <ul class="mt-1 space-y-1">
                                        @foreach ($eventsOn($day) as $event)
                                            <li class="truncate rounded border px-1.5 py-0.5 text-xs {{ $event->isTeachingDay() ? '' : 'bg-muted font-medium' }}"
                                                title="{{ $event->title }}">
                                                {{ $event->title }}
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
            <slot:description>These are the days the school put in front of your family.</slot:description>
            <slot:content>
                @if ($events->isEmpty())
                    <x-empty-state icon="lucide-calendar-days" title="Nothing is on this month"
                        description="Step to another month, or check back once the school publishes its calendar." />
                @else
                    <ul class="divide-y rounded-md border">
                        @foreach ($events as $event)
                            <li class="flex flex-wrap items-start justify-between gap-3 p-4">
                                <div>
                                    <p class="font-medium">{{ $event->title }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ $event->starts_at->format('j M') }}
                                        @if ($event->ends_at->toDateString() !== $event->starts_at->toDateString())
                                            to {{ $event->ends_at->format('j M') }}
                                        @endif
                                        ·
                                        {{ $event->is_all_day ? 'All day' : $event->starts_at->format('H:i').' to '.$event->ends_at->format('H:i') }}
                                        @if (filled($event->location))
                                            · {{ $event->location }}
                                        @endif
                                    </p>
                                    @if (filled($event->description))
                                        <p class="mt-1 whitespace-pre-line text-sm">{{ $event->description }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                        {{ $event->type->label() }}
                                    </span>
                                    @unless ($event->isTeachingDay())
                                        <span class="mt-1 block text-xs text-muted-foreground">The school is shut</span>
                                    @endunless
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
