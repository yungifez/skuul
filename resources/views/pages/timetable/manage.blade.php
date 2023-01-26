@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'timetables' , ],
    ['href'=> route('timetables.manage', $timetable->id), 'text'=> "Manage $timetable->name" , 'active']
]])

@section('title', __("Manage $timetable->name"))

@section('page_heading', __("Manage $timetable->name") )

@section('content')
    @livewire('manage-timetable', ['timetable' => $timetable])
@endsection
