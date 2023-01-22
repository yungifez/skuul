@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('parents.index'), 'text'=> 'Parents', 'active'],
]])

@section('title',  __('Parents'))

@section('page_heading',   __('Parents'))

@section('content', )
    @livewire('list-parents-table')
@endsection