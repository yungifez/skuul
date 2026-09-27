@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.create'), 'text' => 'Create organization', 'active'],
]])

@section('title', __('Create organization'))

@section('page_heading', __('Create organization'))

@section('content')
    @livewire('organization-form')
@endsection
