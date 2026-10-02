@extends('errors.layout')

@section('code', '419')
@section('title', 'Session expired')
@section('message', 'You were away for a while, so the page expired. Reload it and try again. Nothing you typed was saved.')

@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex h-11 select-none items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Reload the page</button>
    <a href="{{ url('/') }}" class="inline-flex h-11 select-none items-center justify-center rounded-md px-4 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground">Go to the dashboard</a>
@endsection
