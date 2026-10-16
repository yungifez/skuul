@php
    $currentAcademicYear = current_school()->load('academicYear')->academicYear;
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.graduations'), 'text'=> "Graduated Students", 'active'],
]])

@section('title', __('Manage Graduations'))

@section('page_heading',  __('Manage Graduations'))

@section('page_actions')
    <x-resource-create-action :href="route('students.graduate')" ability="graduate" :arguments="[\App\Models\Graduation::class]">Graduate learners</x-resource-create-action>
@endsection

@section('content', )

    @livewire('list-graduations-table')
@endsection