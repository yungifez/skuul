@extends('adminlte::page')

@section('title', __('Exams'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Exams') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'Exams', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-exams-table')
    
    @livewire('display-status')
@stop
