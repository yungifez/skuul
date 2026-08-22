@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students', 'active'],
]])

@section('title',  __('Students'))

@section('page_heading',   __('Students'))

@section('page_actions')
    <x-resource-create-action :href="route('students.create')" ability="create" :arguments="[\App\Models\User::class, 'student']">Add student</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-students-table')
@endsection
