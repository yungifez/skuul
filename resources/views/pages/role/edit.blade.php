@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles'],
    ['text' => $role->name, 'active'],
]])

@section('title', $role->name)
@section('page_heading', $role->name)

@section('page_actions')
    <april:button-link href="{{ route('roles.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to roles
    </april:button-link>
@endsection

@section('content')
    @livewire('campus-role-record', ['role' => $role])
@endsection
