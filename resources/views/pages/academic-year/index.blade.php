@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'Academic years' , 'active']
]])

@section('title', __('Academic years'))

@section('page_heading',  __('Academic years'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-years.create')" ability="create" :arguments="[\App\Models\AcademicYear::class]">Add academic year</x-resource-create-action>
@endsection

@section('content', )
    @livewire('set-academic-year')

    @livewire('list-academic-years-table')
@endsection
