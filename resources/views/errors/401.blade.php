@extends('errors.layout')

@section('code', '401')
@section('title', 'Sign in first')
@section('message', 'Sign in to open this page.')

@section('actions')
    <a href="{{ route('login') }}" class="inline-flex h-11 select-none items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Sign in</a>
@endsection
