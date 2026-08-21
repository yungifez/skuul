@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('body')
    <x-partials.authentication-card title="Confirm your password">
        <x-display-validation-errors />
        <livewire:auth.confirm-password-form />
    </x-partials.authentication-card>
@endsection
