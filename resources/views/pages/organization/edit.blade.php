@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.edit', $organization), 'text' => 'Settings', 'active'],
]])

@section('title', __('Organization settings'))

@section('page_heading', __('Organization settings'))

@section('content')
    @livewire('organization-form', ['organization' => $organization])
@endsection
