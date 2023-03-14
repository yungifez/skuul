@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.edit', $feeInvoice->id), 'text'=> $feeInvoice->name , 'active'],
]])

@section('title',  __("Edit $feeInvoice->name"))

@section('page_heading',   __("Edit $feeInvoice->name"))

@section('content', )
    @livewire('edit-fee-invoice-form', ['feeInvoice' => $feeInvoice])
@endsection