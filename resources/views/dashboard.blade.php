@extends('adminlte::page')

@section('title', __('Dashboard'))


@section('content_header')
    <h2 class="h4 font-weight-bold">
        {{ __('Dashboard') }}
    </h2>
@stop

@section('content')
    <x-jet-welcome />
@stop

