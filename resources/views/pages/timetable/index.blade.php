@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('timetables.index'), 'text' => 'Timetables', 'active'],
]])

@section('title', __('Timetables'))
@section('page_heading', __('Timetables'))

@section('page_actions')
    <x-resource-create-action :href="route('timetables.create')" ability="create" :arguments="[\App\Models\Timetable::class]">Add timetable</x-resource-create-action>
@endsection

@section('content')
    @livewire('list-timetables-table')
@endsection
