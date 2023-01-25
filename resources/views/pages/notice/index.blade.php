@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('notices.index'), 'text'=> 'Notices', 'active'],
]])

@section('title', __('Notices'))

@section('page_heading', __('Notices'))

@section('content', )
    @livewire('list-notices-table')
@endsection