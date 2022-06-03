@extends('adminlte::page')

@section('title', __('Create grade-systems'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create grade-systems') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('grade-systems.index'), 'text'=> 'grade-systems'],
        ['href'=> route('grade-systems.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-grade-system-form')

    @livewire('display-status')
@stop
