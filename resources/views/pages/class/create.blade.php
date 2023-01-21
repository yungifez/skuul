@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> ' Classes' ,],
        ['href'=> route('classes.create'), 'text'=> 'Create' , 'active'],
]])

@section('title',__('Create Class'))

@section('page_heading',__('Create Class'))

@section('content')
    @livewire('create-class-form')
@endsection
