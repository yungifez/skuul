@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('classes.index'), 'text'=> 'Classes' , 'active']
]])

@section('title', __('Classes'))

@section('page_heading', __('Classes'))

@section('page_actions')
    <x-resource-create-action :href="route('classes.create')" ability="create" :arguments="[\App\Models\MyClass::class]">Add class</x-resource-create-action>
@endsection

@section('content')
    @livewire('list-classes-table')
@endsection
