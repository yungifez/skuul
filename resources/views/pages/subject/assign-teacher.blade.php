@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('subjects.index'), 'text'=> 'subjects'],
    ['href'=> route('subjects.assign-teacher'), 'text'=> 'Assign teacher', 'active'],
]])

@section('title',  __('Assign teachers to subjects'))

@section('page_heading',   __('Assign teachers to subjects'))

@section('content' )
    @livewire('assign-teacher-to-subjects')
@endsection