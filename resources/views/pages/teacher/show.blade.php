@extends('adminlte::page')

@section('title', __("$teacher->name's profile"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("$teacher->name's profile") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('teachers.index'), 'text'=> 'teachers'],
        ['href'=> route('teachers.show', $teacher->id), 'text'=> "View $teacher->name's profile", 'active'],
    ]])
@endsection

@section('content')

    @livewire('show-teacher-profile', ['teacher' => $teacher])

    @livewire('display-status')

    
@endsection
