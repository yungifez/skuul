@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'Sections', 'active']
]])

@section('title', __('Class Sections'))

@section('page_heading',  __('Class Sections'))

@section('content', )
    @livewire('list-sections-table')
@endsection