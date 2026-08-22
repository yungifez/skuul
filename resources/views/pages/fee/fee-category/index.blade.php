@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fee-categories.index'), 'text'=> 'Fee Categories', 'active'],
]])

@section('title',  __('Fee Categories'))

@section('page_heading',   __('Fee Categories'))

@section('page_actions')
    <x-resource-create-action :href="route('fee-categories.create')" ability="create" :arguments="[\App\Models\FeeCategory::class]">Add fee category</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-fee-categories-table')
@endsection
