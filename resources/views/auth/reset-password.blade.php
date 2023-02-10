@extends('layouts.guest')

@section('title', 'Reset Password')

@section('body')
    <x-partials.authentication-card>
        <x-display-validation-errors />
        <form action="{{route('password.update')}}" class="w-full p-3" method="POST" autocomplete="off">
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-input name="email" id="email" type="email" label="Email" value="{{$request->input('email')}}"/>
            <x-input name="password" id="password" type="password" label="New Password"/>
            <x-input name="password_confirmation" id="password_confirmation" type="password" label="Confirm new password"/>
            @csrf
            <x-button class="my-3 px-10 py-2 rounded-lg">
                Reset Password
            </x-button>
        </form>
       
    </x-partials.authentication-card>
@endsection