@extends('adminlte::page')

@section('title', __('Exam records'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Exam records') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'Exams'],
        ['href'=> route('exam-records.index'), 'text'=> 'Exam records', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-exam-records-table')
    
    @livewire('display-status')
@stop
