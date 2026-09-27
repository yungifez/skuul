@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('data-sharing-requests.index'), 'text' => 'Record sharing'],
    ['text' => 'Ask another school', 'active'],
]])

@section('title', 'Ask another school')
@section('page_heading', 'Ask another school')

@section('page_actions')
    <april:button-link href="{{ route('data-sharing-requests.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to record sharing
    </april:button-link>
@endsection

@section('content')
    @livewire('create-data-sharing-request-form')
@endsection
