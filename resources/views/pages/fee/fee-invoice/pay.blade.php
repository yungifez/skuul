@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('fee-invoices.index'), 'text' => 'Fee invoices'],
    ['href' => route('fee-invoices.show', $feeInvoice->id), 'text' => $feeInvoice->name],
    ['href' => route('fee-invoices.pay', $feeInvoice->id), 'text' => 'Take payment', 'active'],
]])

@section('title', 'Take payment · '.$feeInvoice->name)

@section('page_heading', 'Take payment')

@section('content')
    <livewire:take-invoice-payment :fee-invoice="$feeInvoice" />
@endsection
