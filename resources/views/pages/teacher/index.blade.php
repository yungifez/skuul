@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('teachers.index'), 'text'=> 'Teachers', 'active'],
]])

@section('title',  __('Teachers'))

@section('page_heading',   __('Teachers'))

@section('page_actions')
    <x-resource-create-action :href="route('teachers.create')" ability="create" :arguments="[\App\Models\User::class, 'teacher']">Add teacher</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-teachers-table')
@endsection
