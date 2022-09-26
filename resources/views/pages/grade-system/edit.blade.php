@extends('adminlte::page')

@section('title', __("Edit $gradeSystem->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $gradeSystem->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('grade-systems.index'), 'text'=> 'Grade systems' , ],
        ['href'=> route('grade-systems.edit', $gradeSystem->id), 'text'=> "Edit $gradeSystem->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-grade-system-form', ['grade' => $gradeSystem])

@livewire('display-status')

@endsection
