@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['text' => 'School features', 'active'],
]])

@section('title', 'School features')
@section('page_heading', 'Choose the tools your school uses')

@section('content')
    <livewire:manage-school-features />
@endsection
