@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices', 'active'],
]])

@section('title',  __('Fees Invoices'))

@section('page_heading',   __('Fees Invoices'))

@section('content', )
    @livewire('list-fee-invoices-table')
@endsection