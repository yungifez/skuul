@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('gradebooks.index'), 'text' => 'Gradebooks', 'active'],
]])

@section('title', 'Gradebooks')
@section('page_heading', 'Gradebooks')

@section('content')
    <livewire:gradebook-directory />
@endsection
