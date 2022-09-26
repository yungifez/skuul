@extends('adminlte::page')

@section('title', __('Syllabi'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Syllabi') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('syllabi.index'), 'text'=> 'Syllabi', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-syllabi-table')
    
    @livewire('display-status')
@stop
