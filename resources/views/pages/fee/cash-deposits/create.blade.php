@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['href' => route('cash-deposits.index'), 'text' => 'Cash deposits'],
    ['text' => 'Record cash deposit', 'active'],
]])

@section('title', 'Record cash deposit')
@section('page_heading', 'Record cash deposit')

@section('content')
    <livewire:record-cash-deposit-form />
@endsection
