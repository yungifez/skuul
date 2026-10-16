@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('notices.index'), 'text' => 'Notices'],
    ['href' => route('notices.create'), 'text' => 'Add notice', 'active'],
]])

@section('title', __('Add notice'))

@section('page_heading', __('Add notice'))

@section('content')
    @livewire('create-notice-form')
@endsection
