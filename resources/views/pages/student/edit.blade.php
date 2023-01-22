@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'students' , ],
        ['href'=> route('students.edit', $student->id), 'text'=> "Edit $student->name" , 'active']
]])
@section('title',  __("Edit $student->name"))

@section('page_heading',  __("Edit $student->name"))

@section('content')
    @livewire('edit-student-form', ['student' => $student])
@endsection
