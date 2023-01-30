@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams'],
    ['href'=> route('exam-slots.index' ,$exam->id), 'text'=> 'Exam slots', 'active'],
]])

@section('title',  __("Exam Slots In $exam->name"))

@section('page_heading',   __("Exam Slots In $exam->name"))

@section('content', )
    @livewire('list-exam-slots-table', ['exam'=> $exam])
@endsection