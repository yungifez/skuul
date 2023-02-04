@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams'],
    ['href'=> route('exams.academic-year-result-tabulation'), 'text'=> 'Academic Year Result tabulation', 'active'],
]])

@section('title',    __('Aademic Year Result Tabulation'))

@section('page_heading',  __('Aademic Year Result Tabulation'))

@section('content', )
@livewire('academic-year-result-tabulation')
@endsection