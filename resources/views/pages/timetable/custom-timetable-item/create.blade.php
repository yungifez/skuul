@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
    ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items'],
    ['href'=> route('custom-timetable-items.create'), 'text'=> 'Create custom timetable item', 'active'],
]])

@section('title', __('Create custom timetable item'))

@section('page_heading',  __('Create custom timetable item'))

@section('content' )
    @livewire('create-custom-timetable-item-form')
@endsection