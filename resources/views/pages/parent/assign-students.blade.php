@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('parents.index'), 'text'=> 'Parents'],
    ['href'=> route('parents.assign-student',$parent->id), 'text'=> "Assign students to $parent->name", 'active'],
]])

@section('title',  __("Assign students to $parent->name"))

@section('page_heading', __("Assign students to $parent->name") )

@section('content')
    @livewire('assign-students-to-parent', ['parent' => $parent])
@endsection
