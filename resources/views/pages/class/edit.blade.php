@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> 'Classes' ],
        ['href'=> route('classes.edit', $myClass->id), 'text'=> "Edit $myClass->name" ]
]])
@section('title', __("Edit class $myClass->name"))

@section('page_heading',  __("Edit class $myClass->name "))

@section('content')
    @livewire('edit-class-form', ['myClass' => $myClass])
@endsection
