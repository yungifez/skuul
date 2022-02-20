@extends('adminlte::page')

@section('title', __('teachers'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Teachers') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('teachers.index'), 'text'=> 'teachers', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-teachers-table')
    
    @livewire('display-status')
@stop
