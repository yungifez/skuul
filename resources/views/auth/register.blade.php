@extends('layouts.guest')

@section('title', 'Login')

@section('body')
    <x-partials.authentication-card class="w-full md:w-11/12 lg:w-11/12 xl:w-11/12" width="">
        <x-display-validation-errors />
        <livewire:registration-form />
        <div class="py-6">
            <p>Have An Account? <a href="{{route('login')}}" class="text-blue-800 mx-1"> Login </a></p>
        </div>
    </x-partials.authentication-card>
@endsection