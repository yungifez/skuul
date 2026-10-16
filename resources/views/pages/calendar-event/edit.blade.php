@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('calendar-events.index', ['month' => $event->starts_at->format('Y-m')]), 'text' => 'Calendar'],
    ['text' => $event->title, 'active'],
]])

@section('title', $event->title)
@section('page_heading', $event->title)

@section('content')
    @can('update', $event)
        <livewire:calendar-event-editor :event="$event" />
    @else
        <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
            <dl class="grid grid-cols-2 gap-4 border-y py-4 sm:grid-cols-4">
                <div>
                    <dt class="text-sm text-muted-foreground">Kind</dt>
                    <dd class="font-medium">{{ $event->type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Date</dt>
                    <dd class="font-medium">
                        {{ $event->starts_at->format('j M Y') }}@if ($event->ends_at->toDateString() !== $event->starts_at->toDateString()) – {{ $event->ends_at->format('j M Y') }}@endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Time</dt>
                    <dd class="font-medium">{{ $event->is_all_day ? 'All day' : $event->starts_at->format('H:i').' – '.$event->ends_at->format('H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Where</dt>
                    <dd class="font-medium">{{ $event->location ?? '—' }}</dd>
                </div>
            </dl>
            <p class="text-sm text-muted-foreground">{{ $event->isTeachingDay() ? 'The school teaches on this day.' : 'The school is shut on this day.' }}</p>
            @if (filled($event->description))
                <p class="whitespace-pre-line text-sm">{{ $event->description }}</p>
            @endif
        </div>
    @endcan
@endsection
