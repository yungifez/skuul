@extends('adminlte::page')

@section('title', __('Graduate Students'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Manage Graduations') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.graduate'), 'text'=> "Graduated Students", 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-graduations-table')
    
    @livewire('display-status')
@stop
