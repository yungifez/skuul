@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.pay', $feeInvoice->id), 'text'=> 'Pay', 'active'],
]])

@section('title',  __('Add Payments to Fees Invoice'))

@section('page_heading',   __('Add Payments to Fees Invoice'))

@section('content', )
    @livewire('pay-invoice-form' , compact('feeInvoice'))
@endsection