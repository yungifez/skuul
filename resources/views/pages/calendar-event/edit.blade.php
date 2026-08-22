@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('calendar-events.index'), 'text' => 'Calendar'],
    ['text' => $event->title, 'active'],
]])

@section('title', $event->title)
@section('page_heading', $event->title)

@section('page_actions')
    <april:button-link href="{{ route('calendar-events.index', ['month' => $event->starts_at->format('Y-m')]) }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to the calendar
    </april:button-link>
@endsection

@php
    $canWrite = auth()->user()->can('update', $event);
@endphp

@section('content')
    <div class="space-y-6">
        @unless ($event->is_published)
            <april:alert>
                <slot:icon><x-lucide-file-pen class="size-4" /></slot:icon>
                <slot:title>This is still a draft</slot:title>
                <slot:description>
                    Nobody else reads it, and a draft {{ Str::lower($event->type->label()) }} does not shut the school.
                </slot:description>
            </april:alert>
        @endunless

        <april:card>
            <slot:title>{{ $event->type->label() }}</slot:title>
            <slot:description>
                {{ $event->starts_at->format('j M Y') }}
                @if ($event->ends_at->toDateString() !== $event->starts_at->toDateString())
                    to {{ $event->ends_at->format('j M Y') }}
                @endif
                · added by {{ $event->createdBy?->name ?? 'an unknown person' }}
            </slot:description>
            <slot:content>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                        {{ $event->is_published ? 'On the calendar' : 'Draft' }}
                    </span>
                    <span class="text-sm text-muted-foreground">
                        {{ $event->isTeachingDay() ? 'The school teaches on this day.' : 'The school is shut on this day.' }}
                    </span>

                    @can('publish', $event)
                        <form method="POST" action="{{ route('calendar-events.publication.update', $event) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_published" value="{{ $event->is_published ? '0' : '1' }}">
                            <april:button type="submit" variant="{{ $event->is_published ? 'outline' : 'default' }}" size="sm">
                                @if ($event->is_published)
                                    <x-lucide-eye-off class="mr-1 size-4" />
                                    Make it a draft again
                                @else
                                    <x-lucide-send class="mr-1 size-4" />
                                    Publish it
                                @endif
                            </april:button>
                        </form>
                    @endcan

                    @can('delete', $event)
                        <form method="POST" action="{{ route('calendar-events.destroy', $event) }}" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <april:button type="submit" variant="outline" size="sm">
                                <x-lucide-trash-2 class="mr-1 size-4" />
                                Remove the day
                            </april:button>
                        </form>
                    @endcan
                </div>
            </slot:content>
        </april:card>

        @if ($canWrite)
            <form method="POST" action="{{ route('calendar-events.update', $event) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @include('pages.calendar-event.partials.form', [
                    'event' => $event,
                    'types' => $types,
                    'sections' => $sections,
                    'day' => $event->starts_at,
                ])

                <div class="flex flex-wrap gap-3">
                    <april:button type="submit">
                        <x-lucide-save class="mr-2 size-4" />
                        Save the day
                    </april:button>
                    <april:button-link href="{{ route('calendar-events.index', ['month' => $event->starts_at->format('Y-m')]) }}" variant="outline">
                        Cancel
                    </april:button-link>
                </div>
            </form>
        @else
            <april:card>
                <slot:title>What is on</slot:title>
                <slot:description>You may read this day but not change it.</slot:description>
                <slot:content>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">When</dt>
                            <dd class="text-lg font-semibold">
                                {{ $event->is_all_day ? 'All day' : $event->starts_at->format('H:i').' to '.$event->ends_at->format('H:i') }}
                            </dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Where</dt>
                            <dd class="text-lg font-semibold">{{ $event->location ?? 'Not said' }}</dd>
                        </div>
                    </dl>

                    @if (filled($event->description))
                        <p class="mt-4 whitespace-pre-line text-sm">{{ $event->description }}</p>
                    @endif
                </slot:content>
            </april:card>
        @endif
    </div>
@endsection
