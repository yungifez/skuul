@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> 'Classes'],
        ['href'=> route('classes.show', $class->id), 'text'=> "View $class->name", 'active'],
]])

@section('title', __("View $class->name"))

@section('page_heading', __("View $class->name"))

@section('content')
    @livewire('show-class', ['class' => $class])
@endsection
