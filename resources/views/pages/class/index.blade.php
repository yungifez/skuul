@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('classes.index'), 'text'=> 'Classes' , 'active']
]])

@section('title', __('Classes'))

@section('page_heading', __('Classes'))

@section('content')
    @livewire('list-classes-table')
@endsection
