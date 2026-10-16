@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Section'), 'active'],
]])

@section('title', school_terms('section', 'Section'))
@section('page_heading', school_terms('section', 'Section'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-cycle-sections.create')" ability="create" :arguments="[\App\Models\AcademicCycleSection::class]">Add {{ school_term('section', 'section') }}</x-resource-create-action>
@endsection

@section('content')
    <livewire:section-directory />
@endsection
