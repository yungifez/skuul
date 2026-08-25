@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> school_terms('academic_year', 'School years') , 'active']
]])

@section('title', school_terms('academic_year', 'School years'))

@section('page_heading', school_terms('academic_year', 'School years'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-years.create')" ability="create" :arguments="[\App\Models\AcademicYear::class]">Set up {{ strtolower(school_term('academic_year', 'school year')) }}</x-resource-create-action>
@endsection

@section('content', )
    @livewire('set-academic-year')

    @livewire('list-academic-years-table')
@endsection
