@php
    $currentAcademicYear = auth()->user()->school->load('academicYear')->academicYear;
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.graduate'), 'text'=> "Graduated Students", 'active'],
]])

@section('title', __('Manage Graduations'))

@section('page_heading',  __('Manage Graduations'))

@section('content', )

    @livewire('list-graduations-table')
@endsection