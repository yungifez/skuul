@extends('adminlte::page')

@section('title', __('Create Exam slots'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create Exam slots') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'Exams'],
        ['href'=> route('exam-slots.create',$exam->id), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-exam-slot-form', ['exam' => $exam])

    @livewire('display-status')
@stop
