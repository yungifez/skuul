@extends('adminlte::master')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('body')
    @yield('body')
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
