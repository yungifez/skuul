@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees', 'active'],
]])

@section('title',  __('Fees'))

@section('page_heading',   __('Fees'))

@section('page_actions')
    <x-resource-create-action :href="route('fees.create')" ability="create" :arguments="[\App\Models\Fee::class]">Add fee</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-fees-table')
@endsection
