@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams'],
        ['href'=> route('exams.create'), 'text'=> 'create', 'active'],
]])

@section('title', __('Create exams'))

@section('page_heading',  __('Create exams'))

@section('content' )
    @livewire('create-exam-form')
@endsection