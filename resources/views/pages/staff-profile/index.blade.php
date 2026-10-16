@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-profiles.index'), 'text' => 'Staff directory', 'active'],
]])

@section('title', 'Staff directory')
@section('page_heading', 'Staff directory')

@section('page_actions')
    @can('create', App\Models\StaffProfile::class)
        <april:button-link href="{{ route('staff-profiles.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Add an employment record
        </april:button-link>
    @endcan
@endsection

@section('content')
    <livewire:staff-profile-directory />
@endsection
