@extends('errors.layout')

@section('code', '409')
@section('title', 'Page out of date')
@section('message', $exception->getMessage() ?: 'This page is out of date. Reload it.')

@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex h-11 select-none items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Reload the page</button>
@endsection
