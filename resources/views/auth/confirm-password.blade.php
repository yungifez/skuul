@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('body')
    <x-partials.authentication-card>
        <div class="p-4">
            <x-display-validation-errors />
            <div class="mb-3 text-sm md:text-base">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div>
                    <x-input id="password" type="password" name="password" required autocomplete="current-password" autofocus />
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <x-button class="w-full md:w-1/2">
                        {{ __('Confirm') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-partials.authentication-card>
@endsection