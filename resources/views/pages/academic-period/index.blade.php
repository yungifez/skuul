@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-periods.index'), 'text'=> 'AcademicPeriods', 'active'],
]])

@section('title', __('AcademicPeriods'))

@section('page_heading',  __('AcademicPeriods'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-periods.create')" ability="create" :arguments="[\App\Models\AcademicPeriod::class]">Add academic period</x-resource-create-action>
@endsection

@section('content')
    @livewire('set-academic-period')

    @livewire('list-academic-periods-table')
@endsection
