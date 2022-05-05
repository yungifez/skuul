@extends('adminlte::page')

@section('title', __('Grade systems'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Grade systems') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('grade-systems.index'), 'text'=> 'Grade System', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-grade-systems-table')
    
    @livewire('display-status')
@stop
