@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'timetables' , ],
        ['href'=> route('timetables.edit', $timetable->id), 'text'=> "Edit $timetable->name" , 'active']
]])
@section('title',  __("Edit $timetable->name"))

@section('page_heading',  __("Edit $timetable->name"))

@section('content')
    @livewire('edit-timetable-form', ['timetable' => $timetable])
@endsection
