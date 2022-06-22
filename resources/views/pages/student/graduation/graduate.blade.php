@extends('adminlte::page')

@section('title', __('Graduate Students'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Graduate Students') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.graduate'), 'text'=> "Graduated Students"],
        ['href'=> route('students.graduate'), 'text'=> 'Graduate Students', 'active'],
    ]])

@stop

@section('content') 
    @livewire('graduate-students')
    
    @livewire('display-status')
@stop
