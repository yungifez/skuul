@extends('adminlte::page')

@section('title', __('syllabi'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('syllabi') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('syllabi.index'), 'text'=> 'syllabi', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-syllabi-table')
    
    @livewire('display-status')
@stop
