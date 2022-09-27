@extends('adminlte::page')

@section('title', __('Result Checker'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Result Checker') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams'],
        ['href'=> route('exams.result-checker'), 'text'=> 'Result Checker', 'active'],
    ]])

@stop

@section('content') 
    @livewire('result-checker')

    @livewire('display-status')
@stop
