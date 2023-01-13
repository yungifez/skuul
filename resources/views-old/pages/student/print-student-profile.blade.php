@extends('layouts.print')

@section('title', 'Print Student Profile')

@section('content')
    @livewire('show-student-profile', ['student' => $student])
@endsection

@section('style')
    <style>
        td, th {
            padding: 1rem;
        }
        p {
            text-transform: capitalize;
        }
        .profile-image-wrapper{
           width: min-content; 
           margin-left: auto;
           margin-right: auto;
           text-align: center;
        }
        .profile-image{
            border-radius: 50%;
            margin: inherit;
        }
        
    </style>
@endsection