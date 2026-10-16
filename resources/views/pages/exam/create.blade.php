@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'Exams'],
        ['href'=> route('exams.create'), 'text'=> 'Create exam', 'active'],
]])

@section('title', __('Create exam'))

@section('page_heading',  __('Create exam'))

@section('content' )
    @livewire('create-exam-form')
@endsection