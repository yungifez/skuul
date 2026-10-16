@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['href' => route('student-accounts.show', $enrollment->id), 'text' => $enrollment->user?->name ?? 'Student account', 'active'],
]])

@section('title', __('Student account'))

@section('page_heading', __('Student account'))

@section('content')
    @livewire('show-student-account', ['enrollment' => $enrollment])
@endsection
