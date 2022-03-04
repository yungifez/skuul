@extends('adminlte::page')

@php
    $currentAcademicYear = auth()->user()->school->load('academicYear')->academicYear;
@endphp
@section('title', __('Manage Promotions'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Promote Students') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.promote'), 'text'=> "Promotions for $currentAcademicYear->start_year - $currentAcademicYear->stop_year", 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-promotions-table')
    
    @livewire('display-status')
@stop
