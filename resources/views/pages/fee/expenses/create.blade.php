@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fee-invoices.index'), 'text' => 'Finance'],
    ['href' => route('expenses.index'), 'text' => 'Expenses'],
    ['text' => 'Record expense', 'active'],
]])

@section('title', 'Record expense')
@section('page_heading', 'Record expense')

@section('content')
    <livewire:record-expense-form />
@endsection
