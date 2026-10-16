@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles'],
    ['text' => 'Write a role', 'active'],
]])

@section('title', 'Write a role')
@section('page_heading', 'Write a role')

@section('page_actions')
    <april:button-link href="{{ route('roles.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to roles
    </april:button-link>
@endsection

@section('content')
    @livewire('create-campus-role-form')
@endsection
