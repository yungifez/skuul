@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
    ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items', 'active'],
]])

@section('title', __('Custom timetable items'))

@section('page_heading',  __('Custom timetable items'))

@section('page_actions')
    <x-resource-create-action :href="route('custom-timetable-items.create')" ability="create" :arguments="[\App\Models\CustomTimetableItem::class]">Add timetable item</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-custom-timetable-items-table')
@endsection
