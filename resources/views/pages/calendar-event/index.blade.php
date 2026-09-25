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

@section('content')
    @livewire('calendar-event-directory')
@endsection
