@extends('adminlte::page')

@section('title', __("Edit $semester->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $semester->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('semesters.index'), 'text'=> 'semesters' , ],
        ['href'=> route('semesters.edit', $semester->id), 'text'=> "Edit $semester->name" , 'active']
    ]])
@endSection

@section('content')

@livewire('edit-semester-form', ['semester' => $semester])

@livewire('display-status')

@endSection
