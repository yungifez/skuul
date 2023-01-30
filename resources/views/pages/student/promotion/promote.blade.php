@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.promote'), 'text'=> 'Promote Students', 'active'],
]])

@section('title', __('Promote Students'))

@section('page_heading',  __('Promote Students'))

@section('content' )
    @livewire('promote-students')
@endsection