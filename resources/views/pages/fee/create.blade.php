@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fees.create'), 'text'=> 'Create fee', 'active'],
]])

@section('title',  __('Create fee'))

@section('page_heading',   __('Create fee'))

@section('content', )
    @livewire('create-fee-form')
@endsection