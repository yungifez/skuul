@extends('adminlte::page')

@section('title', __("$student->name's profile"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("$student->name's profile") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.show', $student->id), 'text'=> "View $student->name's profile", 'active'],
    ]])
@endsection

@section('content')
    <a href="{{route('students.print-profile',$student->id)}}" class="btn btn-primary">Print Profile</a>

    @livewire('show-student-profile', ['student' => $student])

    @livewire('display-status')

    
@endsection
