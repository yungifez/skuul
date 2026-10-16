@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi'],
    ['href'=> route('syllabi.show', $syllabus), 'text'=> $syllabus->name],
    ['href'=> route('syllabi.lesson-notes', $syllabus), 'text'=> 'Lesson notes', 'active'],
]])

@section('title', __('Lesson notes for :name', ['name' => $syllabus->name]))

@section('page_heading', __('Lesson notes for :name', ['name' => $syllabus->name]))

@section('content')
    @livewire('lesson-note-book', ['syllabus' => $syllabus])
@endsection
