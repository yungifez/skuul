@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.domains.index', $organization), 'text' => 'Web addresses', 'active'],
]])

@section('title', __('Web addresses'))

@section('page_heading', __('Web addresses'))

@section('content')
    <div class="mx-auto w-full max-w-4xl">
        @livewire('organization-domains', ['organization' => $organization])
    </div>
@endsection
