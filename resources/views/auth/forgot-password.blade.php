@extends('layouts.guest')

@section('title', 'Forgot password')

@section('body')
    <x-partials.authentication-card title="Forgot your password?" description="We will send a reset link to your email address.">
        <x-display-validation-errors />
        <livewire:auth.forgot-password-form />
    </x-partials.authentication-card>
@endsection
