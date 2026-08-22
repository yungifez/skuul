@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi', 'active'],
]])

@section('title',  __('Syllabi'))

@section('page_heading',  __('Syllabi'))

@section('page_actions')
    <x-resource-create-action :href="route('syllabi.create')" ability="create" :arguments="[\App\Models\Syllabus::class]">Add syllabus</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-syllabi-table')
@endsection
