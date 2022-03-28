@extends('adminlte::page')

@section('title', __('Timetables'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Timetables') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-timetables-table')
    
    @livewire('display-status')
@stop
