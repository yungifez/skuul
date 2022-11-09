@extends('adminlte::page')

@section('title', __('Dashboard'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Dashboard') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard', 'active'],
    ]])

@stop

@section('content')
    
    <div class="my-3">@livewire('school-set')</div>
    @livewire('dashboard-data-cards')
    
    @livewire('academic-year-set')

    @if (auth()->user()->hasRole('student'))
        <a href="{{route('students.print-profile',auth()->user()->id)}}" >
            <x-adminlte-small-box title="Download profile" text="click to download profile" icon="fas fa-download text-white"
            theme="secondary"/>
        </a>
    @endif
    
    @can('read notice') 
        @livewire('list-notices-table')
    @endcan

    @if (auth()->user()->hasRole('applicant'))
        {{--Contains status history--}}
        @livewire('change-account-application-status', ['applicant' => auth()->user()])
    @endif

    @livewire('display-status')
@stop

