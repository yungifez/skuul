@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools'],
    ['href'=> route('schools.show', $school), 'text'=> $school->name, 'active'],
]])

@section('title', $school->name)

@section('page_heading', $school->name)

@section('content')
    @livewire('show-school', ['school' => $school])
@endsection
