@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams'],
    ['href'=> route('exams.semester-result-tabulation'), 'text'=> 'Semester Result tabulation', 'active'],
]])

@section('title',    __('Semester result tabulation'))

@section('page_heading',  __('Semester result tabulation'))

@section('content', )
@livewire('semester-result-tabulation')
@endsection