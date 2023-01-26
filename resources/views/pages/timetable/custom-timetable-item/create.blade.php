@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
    ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items'],
    ['href'=> route('custom-timetable-items.create'), 'text'=> 'Create', 'active'],
]])

@section('title', __('Create Custom Timetable Item'))

@section('page_heading',  __('Create Custom Timetable Item'))

@section('content' )
    @livewire('create-custom-timetable-item-form')
@endsection