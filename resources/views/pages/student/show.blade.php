@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.index'), 'text'=> 'Students'],
    ['href'=> route('students.show', $student->id), 'text'=> "View $student->name's profile", 'active'],
]])

@section('title', __("$student->name's profile"))

@section('page_heading', __("$student->name's profile") )

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <april:button-link href="{{ route('students.print-profile', $student) }}" variant="outline">
            <x-lucide-printer class="mr-2 size-4" />
            Open print view
        </april:button-link>
    </div>
    
    @livewire('show-student-profile', ['student' => $student])

    @can('viewAny', App\Models\FeeInvoice::class)
        @livewire('list-student-fee-invoices', ['student' => $student])
    @endcan
@endsection
