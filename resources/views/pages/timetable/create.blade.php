@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
        ['href'=> route('timetables.create'), 'text'=> 'Create timetable', 'active'],
]])

@section('title',  __('Create timetable'))

@section('page_heading',   __('Create timetable'))

@section('content' )
    @livewire('create-timetable-form')
@endsection