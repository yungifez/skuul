@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('semesters.index'), 'text'=> 'semesters' , ],
    ['href'=> route('semesters.edit', $semester->id), 'text'=> "Edit $semester->name" , 'active']
]])
@section('title', __("Edit $semester->name"))

@section('page_heading',  __("Edit $semester->name"))

@section('content')
    @livewire('edit-semester-form', ['semester' => $semester]
)@endsection
