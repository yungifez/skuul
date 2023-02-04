@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account application' ],
        ['href'=> route('account-applications.change-status', $applicant->id), 'text'=> "Change application status of $applicant->name" , 'active']

]])

@section('title',  __('Change application status'))

@section('page_heading',   __('Change application status'))

@section('content' )
@livewire('change-account-application-status', ['applicant' => $applicant])
@endsection