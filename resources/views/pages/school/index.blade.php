@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools' , 'active']
]])

@section('title', __('All schools'))

@section('page_heading', 'All schools')

@section('page_actions')
    <x-resource-create-action :href="route('schools.create')" ability="create" :arguments="[\App\Models\School::class]">Add school</x-resource-create-action>
@endsection

@section('content', )
    @livewire('set-school')
    
    @livewire('list-schools-table')
@endsection
