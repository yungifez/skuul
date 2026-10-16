@php
    $currentAcademicYear = current_school()->load('academicYear')->academicYear;
@endphp

@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.promote'), 'text'=> "Promotions for $currentAcademicYear->name", 'active'],

]])

@section('title', __('Manage Promotions'))

@section('page_heading',  __('Manage Promotions'))

@section('content', )

    @livewire('list-promotions-table',['academicYear'=> $currentAcademicYear->id])
@endsection