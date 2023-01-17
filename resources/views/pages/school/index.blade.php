@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools' , 'active']
]])

@section('title', __('All schools'))

@section('page_heading', 'All schools')

@section('content', )


@endsection