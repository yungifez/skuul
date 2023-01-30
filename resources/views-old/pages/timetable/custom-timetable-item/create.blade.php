@extends('adminlte::page')

@section('title', __('Create Custom Timetable Item'))


@section('content_header')
    <h1 class=""> 
        {{ __('Create Custom Timetable Item') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
        ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items'],
        ['href'=> route('custom-timetable-items.create'), 'text'=> 'Create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-custom-timetable-item-form')

    @livewire('display-status')
@stop
