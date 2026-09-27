@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-profiles.index'), 'text' => 'Staff'],
    ['text' => $profile->user?->name ?? 'Employment record', 'active'],
]])

@section('title', 'Employment record')
@section('page_heading', $profile->user?->name ?? 'Employment record')

@section('page_actions')
    <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to staff
    </april:button-link>
@endsection

@section('content')
    @livewire('staff-profile-record', ['profile' => $profile])
@endsection
