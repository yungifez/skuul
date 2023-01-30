@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams' , ],
        ['href'=> route('exams.edit', $exam->id), 'text'=> "Edit $exam->name" , 'active']
]])
@section('title', __("Edit $exam->name"))

@section('page_heading', __("Edit $exam->name"))

@section('content')
    @livewire('edit-exam-form', ['exam' => $exam])
@endsection
