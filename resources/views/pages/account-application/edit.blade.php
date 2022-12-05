@extends('adminlte::page')

@section('title', __("Edit $applicant->name"))

@section('content_header')
    <h1 class="">
        {{ __("Edit $applicant->name's application") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account applications' , ],
        ['href'=> route('account-applications.edit', $applicant->id), 'text'=> "Edit ".$applicant->firstname()."'s application" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-account-application-form', ['applicant' => $applicant])

@livewire('display-status')

@endsection
