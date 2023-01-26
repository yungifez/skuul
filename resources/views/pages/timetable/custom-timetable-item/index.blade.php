@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
    ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items', 'active'],
]])

@section('title', __('Custom timetable items'))

@section('page_heading',  __('Custom timetable items'))

@section('content', )
    @livewire('list-custom-timetable-items-table')
@endsection