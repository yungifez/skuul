@extends('adminlte::page')

@section('title', __('Create exams'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create exams') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams'],
        ['href'=> route('exams.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-exam-form')

    @livewire('display-status')
@stop
