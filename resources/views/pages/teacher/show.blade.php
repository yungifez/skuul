@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('teachers.index'), 'text'=> 'Teachers'],
    ['href'=> route('teachers.show', $teacher->id), 'text'=> "View $teacher->name's profile", 'active'],
]])

@section('title', __("$teacher->name's profile"))

@section('page_heading', __("$teacher->name's profile") )

@section('content')
    @livewire('show-teacher-profile', ['teacher' => $teacher])
@endsection
