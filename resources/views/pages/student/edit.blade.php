@extends('adminlte::page')

@section('title', __("Edit $student->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $student->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'students' , ],
        ['href'=> route('students.edit', $student->id), 'text'=> "Edit $student->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-student-form', ['student' => $student])

@livewire('display-status')

@endsection
