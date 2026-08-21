@extends('layouts.guest')

@section('title', 'Set up your account')

@section('body')
    <x-partials.authentication-card
        title="Set up your account"
        description="Choose a password to finish setting up your {{ config('app.name') }} account."
    >
        <x-display-validation-errors />
        <livewire:auth.accept-invitation-form :token="$token" :user="$user" />
        <x-slot:footer>
            This link works one time only. If it stops working, ask your school's administrator for a new invitation.
        </x-slot:footer>
    </x-partials.authentication-card>
@endsection
