@extends('errors.layout')

@section('code', '503')
@section('title', 'Back soon')
@section('message', 'Skuul is being updated. Try again in a few minutes.')

@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex h-11 select-none items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Try again</button>
@endsection
