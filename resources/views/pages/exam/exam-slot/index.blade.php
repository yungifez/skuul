@extends('adminlte::page')

@section('title', __('Exam slots'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Exam slots') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'Exams'],
        ['href'=> route('exam-slots.index' ,$exam->id), 'text'=> 'Exam slots', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-exam-slots-table', ['exam'=> $exam])
    
    @livewire('display-status')
@stop
