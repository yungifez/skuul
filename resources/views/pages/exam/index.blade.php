@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams', 'active'],
]])

@section('title',  __('Exams'))

@section('page_heading',   __('Exams'))

@section('page_actions')
    <x-resource-create-action :href="route('exams.create')" ability="create" :arguments="[\App\Models\Exam::class]">Add exam</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-exams-table')
@endsection
