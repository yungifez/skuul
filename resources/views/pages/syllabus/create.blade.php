@extends('adminlte::page')

@section('title', __('Create Syllabus'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create Syllabus') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('syllabi.index'), 'text'=> 'syllabi'],
        ['href'=> route('syllabi.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-syllabus-form')

    @livewire('display-status')
@stop
