@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('class-groups.index'), 'text'=> 'Class Groups' , 'active']
]])

@section('title', __('Class Groups'))

@section('page_heading', __('Class Groups'))

@section('page_actions')
    <x-resource-create-action :href="route('class-groups.create')" ability="create" :arguments="[\App\Models\ClassGroup::class]">Add class group</x-resource-create-action>
@endsection

@section('content')
    @livewire('list-class-groups-table')
@endsection
