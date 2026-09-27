@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['text' => 'School language', 'active'],
]])

@section('title', 'School language')
@section('page_heading', 'Use the words your school uses')

@section('content')
    <livewire:edit-school-language :setup="request()->boolean('setup')" />
@endsection
