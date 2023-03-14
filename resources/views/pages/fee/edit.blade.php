@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> "Fees"],
    ['href'=> route('fees.edit', $fee->id), 'text'=> "Edit $fee->name", 'active'],
]])

@section('title',  __("Edit $fee->name"))

@section('page_heading',   __("Edit $fee->name"))

@section('content', )
    @livewire('edit-fee-form', ['fee' => $fee])
@endsection