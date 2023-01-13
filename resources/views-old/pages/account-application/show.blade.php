@extends('adminlte::page')

@section('title', __("View $applicant->name's application"))

@section('content_header')
    <h1 class="">
        {{ __("View $applicant->name's application") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account Applications'],
        ['href'=> route('account-applications.show', $applicant->id), 'text'=> "View $applicant->name", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-account-application', ['applicant' => $applicant])

    @livewire('display-status')

@endsection
