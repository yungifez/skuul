@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('parents.index'), 'text'=> 'Parents'],
    ['href'=> route('parents.show', $parent->id), 'text'=> "View $parent->name's profile", 'active'],
]])

@section('title', __("$parent->name's profile"))

@section('page_heading', __("$parent->name's profile") )

@section('content')
    @livewire('show-parent-profile', ['parent' => $parent])
@endsection
