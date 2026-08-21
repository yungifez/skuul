@extends('layouts.guest')

@section('title', 'Two Factor Challenge')

@section('body')
    <x-partials.authentication-card title="Two-step verification">
        <x-display-validation-errors />
        <livewire:auth.two-factor-challenge-form />
    </x-partials.authentication-card>
@endsection
           
