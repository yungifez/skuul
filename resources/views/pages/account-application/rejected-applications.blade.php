@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('account-applications.index'), 'text'=> 'Account Applications', 'active'],
]])

@section('title',  __('Account Applications'))

@section('page_heading',   __('Account Applications'))

@section('content', )
    @livewire('list-rejected-account-applications-table')
@endsection