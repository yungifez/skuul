@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'Sections'],
        ['href'=> route('sections.create'), 'text'=> 'Create', 'active'],
]])

@section('title', __('Create Class Section'))

@section('page_heading',  __('Create Class Section'))

@section('content' )
    @livewire('create-section-form')
@endsection