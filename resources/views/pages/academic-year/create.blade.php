@extends('adminlte::page')

@section('title', __('Create academic year'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Create academic year') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text'=> ' Academic years' ,],
        ['href'=> route('academic-years.create'), 'text'=> 'Create' , 'active'],
    ]])
@endsection

@section('content')

    @livewire('create-academic-year-form')

    @livewire('display-status')
@endsection
