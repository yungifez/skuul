@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('timetables.index'), 'text' => 'Timetables'],
    ['href' => route('timetables.manage', $timetable->id), 'text' => "Build {$timetable->name}", 'active'],
]])

@section('title', __("Build {$timetable->name}"))
@section('page_heading', __("Build {$timetable->name}"))

@section('page_actions')
    <div class="flex flex-wrap items-center gap-3">
        <x-timetable-status-control :timetable="$timetable" />
        <april:button-link href="{{ route('timetables.show', $timetable) }}" variant="outline">
            <x-lucide-eye class="mr-2 size-4" />
            View
        </april:button-link>
    </div>
@endsection

@section('content')
    @livewire('manage-timetable', ['timetable' => $timetable])
@endsection
