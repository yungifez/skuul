@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools'],
    ['href'=> route('schools.edit', $school->id), 'text'=> 'Edit school' , 'active']
]])
@section('title', __('Edit school'))

@section('page_heading', __('Edit school details'))

@section('content')
    @livewire('edit-school-form', ['school' => $school, 'setup' => $setup])
@endsection
