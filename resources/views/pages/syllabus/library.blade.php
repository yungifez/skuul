@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi'],
    ['href'=> route('syllabi.library'), 'text'=> 'Curriculum library', 'active'],
]])

@section('title', __('Curriculum library'))

@section('page_heading', __('Curriculum library'))

@section('content')
    @livewire('curriculum-library')
@endsection
