@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.show', $feeInvoice->id), 'text'=> $feeInvoice->name, 'active'],
]])

@section('title', $feeInvoice->name)

@section('page_heading', $feeInvoice->name)

@section('content')
    <livewire:show-fee-invoice :fee-invoice="$feeInvoice" />
@endsection
