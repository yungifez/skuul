@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups'],
    ['text' => 'Make a group', 'active'],
]])

@section('title', 'Make a group')
@section('page_heading', 'Make a group')

@section('page_actions')
    <april:button-link href="{{ route('cohorts.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to groups
    </april:button-link>
@endsection

@section('content')
    @livewire('create-cohort-form')
@endsection
