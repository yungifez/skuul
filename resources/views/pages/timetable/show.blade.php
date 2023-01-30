@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'timetables'],
    ['href'=> route('timetables.show', $timetable->id), 'text'=> "View $timetable->name", 'active'],
]])

@section('title', __("View $timetable->name"))

@section('page_heading', __("View $timetable->name") )

@section('content')
    <a href="{{route('timetables.print',$timetable->id)}}" class="bg-blue-600 py-2 px-4 text-white rounded">Print Timetable</a>
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection
