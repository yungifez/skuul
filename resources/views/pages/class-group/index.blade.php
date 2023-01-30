@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('class-groups.index'), 'text'=> 'Class Groups' , 'active']
]])

@section('title', __('Class Groups'))

@section('page_heading', __('Class Groups'))

@section('content')
    @livewire('list-class-groups-table')
@endsection
