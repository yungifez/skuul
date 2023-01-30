@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams' , ],
    ['href'=> route('exam-slots.index', [ $exam]), 'text'=> 'exam slots' , ],
    ['href'=> route('exam-slots.edit', [$exam, $examSlot->id]), 'text'=> "Edit $examSlot->name" , 'active']
]])
@section('title', __("Edit $examSlot->name"))

@section('page_heading', __("Edit $examSlot->name"))

@section('content')
    @livewire('edit-exam-slot-form', ['exam' => $exam, 'examSlot' => $examSlot])
@endsection
