@extends('layouts.app', ['breadcrumbs' => [
	['href'=> route('dashboard'), 'text'=> 'Dashboard'],
	['href'=> route('account-applications.index'), 'text'=> 'Account applications' , ],
	['href'=> route('account-applications.edit', $applicant->id), 'text'=> "Edit ".$applicant->firstname()."'s application" , 'active']

]])
@section('title', __("Edit $applicant->name's application"))

@section('page_heading', __("Edit $applicant->name's application"))

@section('content')
@livewire('edit-account-application-form', ['applicant' => $applicant])
@endsection
