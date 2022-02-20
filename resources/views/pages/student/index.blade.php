@extends('adminlte::page')

@section('title', __('Students'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Students') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-students-table')
    
    @livewire('display-status')
@stop
