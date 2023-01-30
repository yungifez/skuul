@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'exams'],
    ['href'=> route('exams.result-checker'), 'text'=> 'Result Checker', 'active'],
]])

@section('title',   __('Result Checker'))

@section('page_heading', __('Result Checker'))

@section('content', )
    @livewire('result-checker')
@endsection