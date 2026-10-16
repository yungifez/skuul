@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('notices.index'), 'text'=> 'Notices', 'active'],
]])

@section('title', __('Notices'))

@section('page_heading', __('Notices'))

@section('page_actions')
    <x-resource-create-action :href="route('notices.create')" ability="create" :arguments="[\App\Models\Notice::class]">Add notice</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-notices-table')
@endsection
