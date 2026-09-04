@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('notices.index'), 'text' => 'Notices'],
    ['href' => route('notices.create'), 'text' => 'Create notice', 'active'],
]])

@section('title', __('Create notice'))

@section('page_heading', __('Create notice'))

@section('content')
    @livewire('create-notice-form')
@endsection
