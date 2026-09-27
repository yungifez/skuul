@extends('errors::minimal')

@section('title', 'Page out of date')
@section('code', '409')
@section('message', $exception->getMessage() ?: 'This page is out of date. Reload it.')
