@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases'],
    ['text' => 'Record a case', 'active'],
]])

@section('title', 'Record a case')
@section('page_heading', 'Record a case')

@section('content')
    @livewire('create-incident')
@endsection
