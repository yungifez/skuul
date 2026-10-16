@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fee-categories.index'), 'text'=> 'Fee Categories'],
    ['href'=> route('fee-categories.create'), 'text'=> 'Create fee category', 'active'],
]])

@section('title',  __('Create fee category'))

@section('page_heading',   __('Create fee category'))

@section('content', )
    @livewire('create-fee-category-form')
@endsection