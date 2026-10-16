@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('data-sharing-requests.index'), 'text' => 'Record sharing'],
    ['text' => 'Request', 'active'],
]])

@section('title', 'Record sharing request')
@section('page_heading', 'Record sharing request')

@section('content')
    <livewire:show-data-sharing-request :sharing-request="$sharingRequest" />
@endsection
