@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.members.index', $organization), 'text' => 'Members', 'active'],
]])

@section('title', __('Organization members'))

@section('page_heading', __('Organization members'))

@section('content')
    <livewire:organization-members :organization="$organization" />
@endsection
