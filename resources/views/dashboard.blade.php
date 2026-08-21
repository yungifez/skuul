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

@if (auth()->user()->hasRole(\App\Enums\Role::Student))
    <april:card>
        <slot:content class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-md bg-muted">
                    <x-lucide-download class="size-5" />
                </span>
                <div>
                    <p class="font-semibold">Download your profile</p>
                    <p class="text-sm text-muted-foreground">Save a printable copy of your student record.</p>
                </div>
            </div>
            <april:button-link href="{{route('students.print-profile',auth()->user()->id)}}" variant="outline" aria-label="Download Profile">
                Download profile
                <x-lucide-arrow-up-right class="ml-2 size-4" />
            </april:button-link>
        </slot:content>
    </april:card>
@endif

@can('read notice')
    @livewire('list-notices-table')
@endcan

@endsection
