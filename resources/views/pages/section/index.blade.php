@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'Sections', 'active']
]])

@section('title', __('Class Sections'))

@section('page_heading',  __('Class Sections'))

@section('page_actions')
    <x-resource-create-action :href="route('sections.create')" ability="create" :arguments="[\App\Models\Section::class]">Add section</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-sections-table')
@endsection
