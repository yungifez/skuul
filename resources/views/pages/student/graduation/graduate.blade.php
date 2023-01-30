@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.graduations'), 'text'=> "Graduated Students"],
    ['href'=> route('students.graduate'), 'text'=> 'Graduate Students', 'active'],
]])

@section('title',  __('Graduate Students'))

@section('page_heading',   __('Graduate Students'))

@section('content' )
    @livewire('graduate-students')
@endsection