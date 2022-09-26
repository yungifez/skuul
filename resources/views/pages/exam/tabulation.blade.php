@extends('adminlte::page')

@section('title', __('Exam tabulation'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Exam tabulation') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams'],
        ['href'=> route('exams.tabulation'), 'text'=> 'Exam tabulation', 'active'],
    ]])

@stop

@section('content') 
    @livewire('exam-tabulation')

    @livewire('display-status')
@stop
