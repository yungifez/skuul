@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('fee-categories.index'), 'text'=> 'Fee Categories'],
    ['href'=> route('fee-categories.edit', $feeCategory->id), 'text'=> "Edit $feeCategory->name", 'active'],
]])

@section('title',  __("Edit $feeCategory->name"))

@section('page_heading',   __("Edit $feeCategory->name"))

@section('content', )
    @livewire('edit-fee-category-form',['feeCategory' => $feeCategory])
@endsection