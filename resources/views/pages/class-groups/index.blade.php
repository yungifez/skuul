@extends('adminlte::page')

@section('title', __('Class Groups'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Class Groups') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'Class Groups' , 'active']
    ]])
@endsection

@section('content')

    @livewire('list-class-groups-table')

    @livewire('display-status')
@endsection
