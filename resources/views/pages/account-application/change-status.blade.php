@extends('adminlte::page')

@section('title', __('Change application status'))

@section('content_header')
    <h1 class="">
        {{ __('Change application status') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account application
        ' ],
        ['href'=> route('account-applications.change-status', $applicant->id), 'text'=> "Change application status of $applicant->name" , 'active']
    ]])
@endsection

@section('content')

    @livewire('change-account-application-status', ['applicant' => $applicant])

    @livewire('display-status')
@endsection
