@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('profile.show'), 'text'=> 'Profile' , 'active']
]])

@section('title',  __('Profile'))

@section('page_heading',  __('Profile'))

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div id="update-password">
                @livewire('profile.update-password-form')
            </div>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            @livewire('profile.two-factor-authentication-form')
        @endif

        @livewire('profile.logout-other-browser-sessions-form')

        @if (current_school() !== null)
            <x-partials.action-section>
                <x-slot name="title">Notice email</x-slot>
                <x-slot name="description">Choose whether this school emails you its notices.</x-slot>
                <x-slot name="content">
                    <april:button-link variant="outline" href="{{ route('notice-preferences.edit') }}">Open notice email settings</april:button-link>
                </x-slot>
            </x-partials.action-section>
        @endif
    </div>
@endsection
