@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.billing-groups.index', $organization), 'text' => 'Billing groups', 'active'],
]])

@section('title', __('Billing groups'))

@section('page_heading', __('Which campuses keep one purse'))

@section('content')
    <div class="mx-auto w-full max-w-4xl">
        @livewire('organization-billing-groups', ['organization' => $organization])
    </div>
@endsection
