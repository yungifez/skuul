@extends('adminlte::page')

@section('title', __("Edit Class Group $classGroup->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{  __("Edit Class Group $classGroup->name")}}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'class group' ],
        ['href'=> route('class-groups.edit', $classGroup->id), 'text'=> 'Edit' , 'active']
    ]])
@endsection

@section('content')

    @livewire('edit-class-group-form', ['classGroup' => $classGroup])

    @livewire('display-status')
@endsection
