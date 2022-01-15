@extends('adminlte::page')

@section('title', __("Edit $subject->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $subject->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('subjects.index'), 'text'=> 'Subjects' , ],
        ['href'=> route('subjects.edit', $subject->id), 'text'=> "Edit $subject->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-subject-form', ['subject' => $subject])

@livewire('display-status')

@endsection
