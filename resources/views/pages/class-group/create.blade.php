@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'Class Groups' ],
        ['href'=> route('class-groups.create'), 'text'=> 'Create', 'active'],
]])

@section('title',__('Create Class Group'))

@section('page_heading',__('Create Class Group'))

@section('content')
    @livewire('create-class-group-form')
@endsection
