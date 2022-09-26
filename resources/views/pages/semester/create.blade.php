@extends('adminlte::page')

@section('title', __('Create Semester'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create semester') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('semesters.index'), 'text'=> 'semesters'],
        ['href'=> route('semesters.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-semester-form')

    @livewire('display-status')
@stop
