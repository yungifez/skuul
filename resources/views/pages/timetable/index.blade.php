@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables', 'active'],
]])

@section('title',  __('Timetables'))

@section('page_heading',   __('Timetables'))

@section('content', )
    @livewire('list-timetables-table')
@endsection