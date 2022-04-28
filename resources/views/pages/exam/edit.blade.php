@extends('adminlte::page')

@section('title', __("Edit $exam->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $exam->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams' , ],
        ['href'=> route('exams.edit', $exam->id), 'text'=> "Edit $exam->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-exam-form', ['exam' => $exam])

@livewire('display-status')

@endsection
