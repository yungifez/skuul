@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups'],
    ['text' => $cohort->name, 'active'],
]])

@section('title', $cohort->name)
@section('page_heading', $cohort->name)

@section('page_actions')
    <april:button-link href="{{ route('cohorts.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to groups
    </april:button-link>
@endsection

@section('content')
    @livewire('cohort-record', ['cohort' => $cohort])
@endsection
