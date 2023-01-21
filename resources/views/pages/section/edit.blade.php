@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'sections' , ],
        ['href'=> route('sections.edit', $section->id), 'text'=> "Edit $section->name" , 'active']
]])
@section('title', __("Edit $section->name"))

@section('page_heading', __("Edit $section->name"))

@section('content')
    @livewire('edit-section-form', ['section' => $section])
@endsection
