@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('fee-invoices.index'), 'text' => 'Fee invoices'],
    ['href' => route('student-accounts.show', $enrollment->id), 'text' => $enrollment->user?->name ?? 'Student account', 'active'],
]])

@section('title', __('Student account'))

@section('page_heading', __('Student account'))

@section('content')
    @livewire('show-student-account', ['enrollment' => $enrollment])
@endsection
