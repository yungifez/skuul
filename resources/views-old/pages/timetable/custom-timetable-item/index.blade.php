@extends('adminlte::page')

@section('title', __('Custom timetable items'))


@section('content_header')
    <h1 class=""> 
        {{ __('Custom timetable items') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
        ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-custom-timetable-items-table')
    
    @livewire('display-status')
@stop
