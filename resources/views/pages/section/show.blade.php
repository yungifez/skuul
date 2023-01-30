@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('sections.index'), 'text'=> 'sections'],
    ['href'=> route('sections.show', $section->id), 'text'=> "View $section->name details", 'active'],
]])

@section('title', __("View $section->name details"))

@section('page_heading', __("View $section->name details") )

@section('content')
    @livewire('show-section', ['section' => $section])
@endsection
