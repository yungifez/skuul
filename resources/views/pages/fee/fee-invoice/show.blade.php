@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices'],
    ['href'=> route('fee-invoices.show', $feeInvoice->id), 'text'=> $feeInvoice->name, 'active'],
]])

@section('title',  __($feeInvoice->name))

@section('page_heading',   __($feeInvoice->name))

@section('content', )
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <april:button-link href="{{ route('fee-invoices.print', $feeInvoice) }}" variant="outline">
            <x-lucide-printer class="mr-2 size-4" />
            Open print view
        </april:button-link>
    </div>
    @livewire('show-fee-invoice', ['feeInvoice' => $feeInvoice])
@endsection
