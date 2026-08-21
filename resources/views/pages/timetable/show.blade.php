@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'timetables'],
    ['href'=> route('timetables.show', $timetable->id), 'text'=> "View $timetable->name", 'active'],
]])

@section('title', __("View $timetable->name"))

@section('page_heading', __("View $timetable->name") )

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <x-timetable-status-control :timetable="$timetable" />
        <april:button variant="outline" type="button" onclick="window.location='{{ route('timetables.print', $timetable->id) }}'">
            <x-lucide-printer class="mr-2 size-4" />
            Print timetable
        </april:button>
    </div>
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection
