@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('support-plans.index'), 'text' => 'Support plans'],
    ['text' => 'Open a plan', 'active'],
]])

@section('title', 'Open a support plan')
@section('page_heading', 'Open a support plan')

@section('content')
    @livewire('create-support-plan')
@endsection
