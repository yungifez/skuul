@extends('adminlte::page')

@section('title', __("Edit $teacher->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $teacher->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('teachers.index'), 'text'=> 'teachers' , ],
        ['href'=> route('teachers.edit', $teacher->id), 'text'=> "Edit $teacher->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-teacher-form', ['teacher' => $teacher])

@livewire('display-status')

@endsection
