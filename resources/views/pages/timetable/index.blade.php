@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('timetables.index'), 'text' => 'Timetables', 'active'],
]])

@section('title', __('Timetables'))
@section('page_heading', __('Timetables'))

@section('page_actions')
    @if (auth()->user()->hasRole(\App\Enums\Role::Teacher))
        <april:button-link href="{{ route('timetables.week') }}" variant="outline">My week</april:button-link>
    @endif
    <x-resource-create-action :href="route('timetables.create')" ability="create" :arguments="[\App\Models\Timetable::class]">Add timetable</x-resource-create-action>
@endsection

@section('content')
    @livewire('list-timetables-table')
@endsection
