@extends('layouts.app', ['breadcrumbs' => array_values(array_filter([
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    auth()->user()->can('viewAny', App\Models\School::class) ? ['href'=> route('schools.index'), 'text'=> 'Schools'] : null,
    ['href'=> route('schools.create'), 'text'=> 'Create school', 'active'],
]))])

@section('title', __('Create school'))

@section('page_heading',  __('Create school'))

@section('content' )
    @livewire('create-school-form')
@endsection
