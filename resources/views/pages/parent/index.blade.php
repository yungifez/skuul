@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('parents.index'), 'text'=> 'Parents', 'active'],
]])

@section('title',  __('Parents'))

@section('page_heading',   __('Parents'))

@section('page_actions')
    <x-resource-create-action :href="route('parents.create')" ability="create" :arguments="[\App\Models\User::class, 'parent']">Add parent</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-parents-table')
@endsection
