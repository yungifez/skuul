@extends('adminlte::page')

@section('title', __("Manage $timetable->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Manage $timetable->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'timetables' , ],
        ['href'=> route('timetables.manage', $timetable->id), 'text'=> "Manage $timetable->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('manage-timetable', ['timetable' => $timetable])

@livewire('display-status')

@endsection
