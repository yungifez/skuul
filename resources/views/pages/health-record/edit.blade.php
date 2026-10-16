@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('health-records.index'), 'text' => 'Health records'],
    ['text' => $enrollment->user?->name ?? $enrollment->admission_number, 'active'],
]])

@section('title', 'Health record')
@section('page_heading', $enrollment->user?->name ?? $enrollment->admission_number)

@section('page_actions')
    <april:button-link href="{{ route('health-records.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to health records
    </april:button-link>
@endsection

@section('content')
    @livewire('health-record-form', ['enrollment' => $enrollment])
@endsection
