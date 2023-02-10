@extends('layouts.guest')

@section('title', 'Forgot password')

@section('body')
    <x-partials.authentication-card>
        @if (session('status'))
            <x-alert colour="bg-green-500" title="Success" icon="fa fa-check" dismissOnTimeout="true" >
                {{ session('status') }}
            </x-alert>
        @endif
        <x-display-validation-errors />
        <form action="{{route('password.email')}}" class="p-7 border-b" method="POST">
            <p class="text-gray-600">
                Forgot your password? 
                No problem. Just let us 
                know your email address 
                and we will email 
                you a password reset 
                link that will allow 
                you to choose a new one.
            </p>
            <x-input type="email" name="email" id="email" />
            @csrf
            <div class="flex justify-end">
                <x-button class="my-3 py-2 rounded-lg">
                    Email Password Reset Link
                </x-button>
            </div>
        </form>
        <div class="p-3">
            <p>Back to <a class="text-blue-700" href="{{route('login')}}" aria-label="Login">Login page</a></p>
        </div>
       
    </x-partials.authentication-card>
@endsection