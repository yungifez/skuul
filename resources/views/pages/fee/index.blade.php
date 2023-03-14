@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fees.index'), 'text'=> 'Fees', 'active'],
]])

@section('title',  __('Fees'))

@section('page_heading',   __('Fees'))

@section('content', )
    @livewire('list-fees-table')
@endsection