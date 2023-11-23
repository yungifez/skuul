@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard', 'active'],
]])

@section('title', __('Dashboard'))

@section('page_heading', 'Dashboard')

@section('content')

@can('set school')
    @livewire('set-school')
@endcan

@livewire('dashboard-data-cards')

@livewire('set-academic-year')

@if (auth()->user()->hasRole('student'))
    <a href="{{route('students.print-profile',auth()->user()->id)}}" aria-label="Download Profile">
        <div class="card bg-purple-500 dark:bg-purple-600 text-white md:text-2xl">
            <div class="card-body flex gap-4 items-center justify-center">
                <i class="fa fa-download" aria-hidden="true"></i>
                <p class="font-bold">Download Profile</p>
            </div>
        </div>
    </a>
@endif

@can('read notice')
    @livewire('list-notices-table')
@endcan

@if (auth()->user()->hasRole('applicant'))
    @livewire('application-history', ['applicant' => auth()->user()])
@endif

@can('read applicant')
    @livewire('list-account-applications-table')
@endcan

@endsection
