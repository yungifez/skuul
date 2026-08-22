@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('calendar-events.index'), 'text' => 'Calendar'],
    ['text' => 'Add a day', 'active'],
]])

@section('title', 'Add a day to the calendar')
@section('page_heading', 'Add a day to the calendar')

@section('page_actions')
    <april:button-link href="{{ route('calendar-events.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to the calendar
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('calendar-events.store') }}" class="space-y-6">
        @csrf

        <april:alert>
            <slot:icon><x-lucide-file-pen class="size-4" /></slot:icon>
            <slot:title>This saves as a draft</slot:title>
            <slot:description>
                Nobody else sees the day until it is published, and a draft closure does not shut the school.
            </slot:description>
        </april:alert>

        @include('pages.calendar-event.partials.form', [
            'event' => null,
            'types' => $types,
            'sections' => $sections,
            'day' => $day,
        ])

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-calendar-plus class="mr-2 size-4" />
                Save as a draft
            </april:button>
            <april:button-link href="{{ route('calendar-events.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
