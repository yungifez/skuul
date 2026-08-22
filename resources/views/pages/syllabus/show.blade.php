@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'syllabi'],
    ['href'=> route('syllabi.show', $syllabus->id), 'text'=> "View $syllabus->name", 'active'],
]])

@section('title', __("View $syllabus->name"))

@section('page_heading', __("View $syllabus->name") )

@section('content')
    @livewire('show-syllabus', ['syllabus' => $syllabus])
@endsection
