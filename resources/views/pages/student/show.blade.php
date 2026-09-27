@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('students.index'), 'text' => 'Students'],
    ['href' => route('students.show', $student->id), 'text' => $student->name, 'active'],
]])

@section('title', $student->name)

@section('page_heading', $student->name)

@section('page_actions')
    <april:button-link href="{{ route('students.print-profile', $student) }}" variant="ghost">
        <x-lucide-printer class="mr-2 size-4" />
        Print
    </april:button-link>
@endsection

@section('content')
    <div class="flex flex-col gap-10">
        @livewire('show-student-profile', ['student' => $student])

        @can('viewAny', App\Models\FeeInvoice::class)
            @livewire('list-student-fee-invoices', ['student' => $student])
        @endcan
    </div>
@endsection
