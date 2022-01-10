@extends('adminlte::page')

@section('title', __('Academic years'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Academic years') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text'=> 'Academic years' , 'active']
    ]])
@endsection

@section('content')

    @livewire('academic-year-set')

    @livewire('list-academic-years-table')

    @livewire('display-status')
@endsection
