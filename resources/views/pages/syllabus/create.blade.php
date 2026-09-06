@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi'],
    ['href'=> route('syllabi.create'), 'text'=> 'Create syllabus', 'active'],
]])

@section('title',__('Create syllabus'))

@section('page_heading', __('Create syllabus'))

@section('content' )
    @livewire('create-syllabus-form')
@endsection