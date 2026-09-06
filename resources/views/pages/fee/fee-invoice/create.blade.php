@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.create'), 'text'=> 'Create fee invoice', 'active'],
]])

@section('title',  __('Create fee invoice'))

@section('page_heading',   __('Create fee invoice'))

@section('content', )
    @livewire('create-fee-invoice-form')
@endsection