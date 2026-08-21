@extends('layouts.guest')

@section('title', 'Reset Password')

@section('body')
    <x-partials.authentication-card title="Reset your password" description="Choose a new password for your account.">
        <x-display-validation-errors />
        <livewire:auth.reset-password-form :request="$request" />
    </x-partials.authentication-card>
@endsection
