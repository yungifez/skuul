@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.show', $student->id), 'text'=> "View $student->name's profile", 'active'],
]])

@section('title', __("$student->name's profile"))

@section('page_heading', __("$student->name's profile") )

@section('content')
    <a href="{{route('students.print-profile',$student->id)}}" class="bg-blue-600 py-2 px-4 text-white rounded">Print Profile</a>
    
    @livewire('show-student-profile', ['student' => $student])

    @can('viewAny', App\Models\FeeInvoice::class)
        @livewire('list-student-fee-invoices', ['student' => $student])
    @endcan
@endsection
