@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'School calendars' , 'active']
]])

@section('title', __('School calendars'))

@section('page_heading',  __('School calendars'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-years.create')" ability="create" :arguments="[\App\Models\AcademicYear::class]">Set up calendar</x-resource-create-action>
@endsection

@section('content', )
    @livewire('set-academic-year')

    @livewire('list-academic-years-table')
@endsection
