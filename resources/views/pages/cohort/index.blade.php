@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups', 'active'],
]])

@section('title', 'Groups')
@section('page_heading', 'Groups')

@section('page_actions')
    @can('create', App\Models\Cohort::class)
        <april:button-link href="{{ route('cohorts.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Make a group
        </april:button-link>
    @endcan
@endsection

@section('content')
    <livewire:cohort-directory />
@endsection
