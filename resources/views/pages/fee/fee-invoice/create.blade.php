@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.create'), 'text'=> 'Create', 'active'],
]])

@section('title',  __('Create Fees Invoice'))

@section('page_heading',   __('Create Fees Invoice'))

@section('content', )
    @livewire('create-fee-invoice-form')
@endsection