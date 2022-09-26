@extends('adminlte::page')

@section('title', __('Class Sections'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Class Sections') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'Sections', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-sections-table')
    
    @livewire('display-status')
@stop
