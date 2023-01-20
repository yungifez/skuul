@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'schools'],
    ['href'=> route('schools.show', $school->id), 'text'=> "View $school->name details", 'active'],
]])

@section('title', __("View $school->name details"))

@section('page_heading', __("View $school->name details") )

@section('content')
    @livewire('show-school', ['school' => $school])
@endsection
