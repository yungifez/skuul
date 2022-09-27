@extends('adminlte::page')

@section('title', __("Edit $examSlot->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $examSlot->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('exams.index'), 'text'=> 'exams' , ],
        ['href'=> route('exam-slots.index', [ $exam]), 'text'=> 'exam-slots' , ],
        ['href'=> route('exam-slots.edit', [$exam, $examSlot->id]), 'text'=> "Edit $examSlot->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-exam-slot-form', ['exam' => $exam, 'examSlot' => $examSlot])

@livewire('display-status')

@endsection
