@extends('adminlte::page')

@section('title', __("Edit $section->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $section->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'sections' , ],
        ['href'=> route('sections.edit', $section->id), 'text'=> "Edit $section->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-section-form', ['section' => $section])

@livewire('display-status')

@endsection
