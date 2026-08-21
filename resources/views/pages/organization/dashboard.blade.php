@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.dashboard', $organization), 'text' => 'Overview', 'active'],
]])

@section('title', __('Organization overview'))

@section('page_heading', $organization->name)

@section('content')
    @livewire('organization-dashboard', ['organization' => $organization])
@endsection
