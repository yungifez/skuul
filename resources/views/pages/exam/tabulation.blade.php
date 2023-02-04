@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams'],
    ['href'=> route('exams.tabulation'), 'text'=> 'Exam tabulation', 'active'],]])

@section('title',   __('Exam tabulation'))

@section('page_heading',    __('Exam tabulation'))

@section('content', )
    @livewire('exam-tabulation')
@endsection