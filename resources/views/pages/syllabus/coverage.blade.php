@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi'],
    ['href'=> route('syllabi.coverage'), 'text'=> 'Coverage', 'active'],
]])

@section('title', __('Syllabus coverage'))

@section('page_heading', __('Syllabus coverage'))

@section('content')
    @livewire('syllabus-coverage-report')
@endsection
