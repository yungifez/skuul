@extends('adminlte::page')

@section('title', __('Profile'))

@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Profile') }}
    </h1>

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('profile.show'), 'text'=> 'Profile' , 'active']
    ]])
@stop

@section('content')
    @livewire('display-status')
    <div>
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')

            <x-jet-section-border />
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div id="update-password">
             @livewire('profile.update-password-form')
        </div>
           

            <x-jet-section-border />
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            @livewire('profile.two-factor-authentication-form')

            <x-jet-section-border />
        @endif

        @livewire('profile.logout-other-browser-sessions-form')

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-jet-section-border />

            @livewire('profile.delete-user-form')
        @endif
    </div>
@endsection

@section('css')
     <!-- Google Font: Source Sans Pro -->
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
@endsection

@section('js')
     <!-- Scripts -->
     <script src="{{ mix('js/app.js') }}" defer></script>
@endsection