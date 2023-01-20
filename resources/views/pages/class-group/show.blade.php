@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('class-groups.index'), 'text'=> 'Class Groups'],
        ['href'=> route('class-groups.show', $classGroup->id), 'text'=> "View $classGroup->name", 'active'],
]])

@section('title', __("View $classGroup->name"))

@section('page_heading', __("View $classGroup->name"))

@section('content')
    @livewire('show-class-group', ['classGroup' => $classGroup])
@endsection
