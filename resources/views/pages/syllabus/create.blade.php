@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'syllabi'],
    ['href'=> route('syllabi.create'), 'text'=> 'create', 'active'],
]])

@section('title',__('Create Syllabus'))

@section('page_heading', __('Create Syllabus'))

@section('content' )
    @livewire('create-syllabus-form')
@endsection