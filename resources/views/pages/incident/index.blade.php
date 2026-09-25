@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases', 'active'],
]])

@section('title', 'Cases')
@section('page_heading', 'Cases')

@section('page_actions')
    @can('create', App\Models\Incident::class)
        <april:button-link href="{{ route('incidents.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Record a case
        </april:button-link>
    @endcan
@endsection

@section('content')
    @livewire('incident-directory')
@endsection
