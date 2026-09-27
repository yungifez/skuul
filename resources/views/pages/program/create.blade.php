@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('programs.index'), 'text' => 'Programmes'],
    ['text' => 'Open a programme', 'active'],
]])

@section('title', 'Open a programme')
@section('page_heading', 'Open a programme')

@section('page_actions')
    <april:button-link href="{{ route('programs.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to programmes
    </april:button-link>
@endsection

@section('content')
    @livewire('create-program-form')
@endsection
