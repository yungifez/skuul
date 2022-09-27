@extends('adminlte::page')

@section('title', __("Edit $parent->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $parent->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'Parents' , ],
        ['href'=> route('parents.edit', $parent->id), 'text'=> "Edit $parent->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-parent-form', ['parent' => $parent])

@livewire('display-status')

@endsection
