@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Finance'],
    ['href'=> route('fee-invoices.show', $feeInvoice), 'text'=> $feeInvoice->name],
    ['text'=> 'Edit', 'active'],
]])

@section('title', 'Edit · '.$feeInvoice->name)

@section('page_heading', $feeInvoice->name)

@section('content')
    @livewire('edit-fee-invoice-form', ['feeInvoice' => $feeInvoice])
@endsection