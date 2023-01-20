@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'class group' ],
        ['href'=> route('class-groups.edit', $classGroup->id), 'text'=> "Edit $classGroup->name" , 'active']
]])

@section('title', __("Edit Class Group $classGroup->name"))

@section('page_heading', __("Edit Class Group $classGroup->name"))

@section('content')
    @livewire('edit-class-group-form', ['classGroup' => $classGroup])
@endsection
