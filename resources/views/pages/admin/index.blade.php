@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('admins.index'), 'text'=> 'Administrators', 'active'],
]])

@section('title',  __('Administrators'))

@section('page_heading',   __('Administrators'))

@section('page_actions')
    <x-resource-create-action :href="route('admins.create')" ability="create" :arguments="[\App\Models\User::class, 'admin']">Add administrator</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-admins-table')
@endsection
