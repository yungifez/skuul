@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('grade-systems.index'), 'text'=> 'Grade System', 'active'],
]])

@section('title', __('Grade systems'))

@section('page_heading',  __('Grade systems'))

@section('page_actions')
    <x-resource-create-action :href="route('grade-systems.create')" ability="create" :arguments="[\App\Models\GradeSystem::class]">Add grade system</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-grade-systems-table')
@endsection
