@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('programs.index'), 'text' => 'Programmes', 'active'],
]])

@section('title', 'Programmes')
@section('page_heading', 'Programmes')

@section('page_actions')
    @can('create', App\Models\Program::class)
        <april:button-link href="{{ route('programs.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Open a programme
        </april:button-link>
    @endcan
@endsection

@section('content')
    <livewire:program-directory />
@endsection
