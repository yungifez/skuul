@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees'],
    ['href'=> route('fees.create'), 'text'=> 'Create', 'active'],
]])

@section('title',  __('Create Fee'))

@section('page_heading',   __('Create Fee'))

@section('content', )
    @livewire('create-fee-form')
@endsection