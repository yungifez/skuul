@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams'],
    ['href'=> route('exam-records.index'), 'text'=> 'Exam records', 'active'],
]])

@section('title',  __('Exam records'))

@section('page_heading',   __('Exam records'))

@section('content', )
    @livewire('list-exam-records-table')
@endsection