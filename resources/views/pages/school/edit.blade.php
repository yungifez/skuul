@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools'],
    ['href'=> route('schools.edit', $school->id), 'text'=> 'Settings' , 'active']
]])
@section('title', __("Edit School $school->name"))

@section('page_heading',  __("Edit School $school->name"))

@section('content')

    @livewire('edit-school-form', ['school' => $school])

    @livewire('display-status')

@endsection
