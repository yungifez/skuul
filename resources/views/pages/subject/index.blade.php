@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('subjects.index'), 'text'=> 'subjects', 'active'],
]])

@section('title', __('Subjects'))

@section('page_heading',  __('Subjects'))

@section('page_actions')
    <x-resource-create-action :href="route('subjects.create')" ability="create" :arguments="[\App\Models\Subject::class]">Add subject</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-subjects-table')
@endsection
