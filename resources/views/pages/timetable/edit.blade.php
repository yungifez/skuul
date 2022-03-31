@extends('adminlte::page')

@section('title', __("Edit $timetable->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $timetable->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'timetables' , ],
        ['href'=> route('timetables.edit', $timetable->id), 'text'=> "Edit $timetable->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-timetable-form', ['timetable' => $timetable])

@livewire('display-status')

@endsection
