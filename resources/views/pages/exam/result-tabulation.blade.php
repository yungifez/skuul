@extends('adminlte::page')

@section('title', __('Semester result tabulation'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Semester result tabulation') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams'],
        ['href'=> route('exams.result-tabulation'), 'text'=> 'Result tabulation', 'active'],
    ]])

@stop

@section('content') 
    @livewire('result-tabulation')

    @livewire('display-status')
@stop
