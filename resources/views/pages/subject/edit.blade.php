@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('subjects.index'), 'text'=> 'Subjects' , ],
    ['href'=> route('subjects.edit', $subject->id), 'text'=> "Edit $subject->name" , 'active']
]])
@section('title', __("Edit $subject->name"))

@section('page_heading', __("Edit $subject->name"))

@section('content')
    @livewire('edit-subject-form', ['subject' => $subject])
@endsection
