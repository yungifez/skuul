@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal-requests.index'), 'text' => 'Family requests', 'active'],
]])

@section('title', 'Family requests')
@section('page_heading', 'Family requests')

@section('content')
    @if ($errors->any())
        <april:alert variant="destructive">
            <slot:title>The request did not move</slot:title>
            <slot:description>{{ $errors->first() }}</slot:description>
        </april:alert>
    @endif

    <livewire:portal-request-inbox />
@endsection
