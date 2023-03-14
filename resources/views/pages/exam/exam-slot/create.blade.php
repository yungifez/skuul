@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams'],
    ['href'=> route('exam-slots.index' ,$exam->id), 'text'=> 'Exam slots'],
    ['href'=> route('exam-slots.create',$exam->id), 'text'=> 'Create', 'active'],
]])

@section('title', __("Create Exam slots in $exam->name"))

@section('page_heading',  __("Create Exam slots in $exam->name"))

@section('content' )
    @livewire('create-exam-slot-form', ['exam' => $exam])
@endsection