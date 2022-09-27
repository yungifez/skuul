@extends('adminlte::page')

@section('title', __('Create Timetable'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create Timetable') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
        ['href'=> route('timetables.create'), 'text'=> 'Create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-timetable-form')

    @livewire('display-status')
@stop
