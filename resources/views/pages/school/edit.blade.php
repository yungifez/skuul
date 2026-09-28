@extends('layouts.app', ['breadcrumbs' => array_values(array_filter([
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    auth()->user()->can('viewAny', App\Models\School::class) ? ['href'=> route('schools.index'), 'text'=> 'Schools'] : null,
    auth()->user()->can('view', $school) ? ['href'=> route('schools.show', $school), 'text'=> $school->name] : null,
    ['href'=> route('schools.edit', $school->id), 'text'=> 'Edit school' , 'active'],
]))])
@section('title', __('Edit school'))

@section('page_heading', __('Edit school details'))

@section('content')
    @livewire('edit-school-form', ['school' => $school, 'setup' => $setup])
@endsection
