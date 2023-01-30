@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'parents' , ],
        ['href'=> route('parents.edit', $parent->id), 'text'=> "Edit $parent->name" , 'active']
]])
@section('title',  __("Edit $parent->name"))

@section('page_heading',  __("Edit $parent->name"))

@section('content')
    @livewire('edit-parent-form', ['parent' => $parent])
@endsection
