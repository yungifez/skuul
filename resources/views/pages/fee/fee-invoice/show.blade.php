@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.show', $feeInvoice->id), 'text'=> $feeInvoice->name, 'active'],
]])

@section('title',  __($feeInvoice->name))

@section('page_heading',   __($feeInvoice->name))

@section('content', )
    <a href="{{route('fee-invoices.print',$feeInvoice->id)}}" class="bg-blue-600 py-2 px-4 text-white rounded">Print Invoice</a>
    @livewire('show-fee-invoice', ['feeInvoice' => $feeInvoice])
@endsection