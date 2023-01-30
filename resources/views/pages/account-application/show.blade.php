@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('account-applications.index'), 'text'=> 'Account Applications'],
    ['href'=> route('account-applications.show', $applicant->id), 'text'=> "View $applicant->name", 'active'],
]])

@section('title', __("View $applicant->name's application"))

@section('page_heading', __("View $applicant->name's application") )

@section('content')
    @livewire('show-account-application', ['applicant' => $applicant])
@endsection
