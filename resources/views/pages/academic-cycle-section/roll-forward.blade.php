@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['href' => route('academic-cycle-sections.roll-forward.show'), 'text' => 'Roll forward', 'active'],
]])

@section('title', __('Roll '.strtolower(school_terms('section', 'Section')).' into another year'))
@section('page_heading', __('Roll '.strtolower(school_terms('section', 'Section')).' into another year'))

@section('content')
    <livewire:roll-forward-sections :setup="request()->boolean('setup')" />
@endsection
