@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams'],
    ['href'=> route('exams.academic-period-result-tabulation'), 'text'=> 'AcademicPeriod Result tabulation', 'active'],
]])

@section('title',    __('AcademicPeriod result tabulation'))

@section('page_heading',  __('AcademicPeriod result tabulation'))

@section('content', )
@livewire('academic-period-result-tabulation')
@endsection