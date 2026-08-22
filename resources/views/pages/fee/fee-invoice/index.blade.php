@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fee-invoices.index'), 'text'=> 'Fee Invoices', 'active'],
]])

@section('title',  __('Fees Invoices'))

@section('page_heading',   __('Fees Invoices'))

@section('page_actions')
    <x-resource-create-action :href="route('fee-invoices.create')" ability="create" :arguments="[\App\Models\FeeInvoice::class]">Add fee invoice</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-fee-invoices-table')
@endsection
