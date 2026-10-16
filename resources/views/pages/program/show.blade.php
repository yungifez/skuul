@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('programs.index'), 'text' => 'Programmes'],
    ['text' => $program->name, 'active'],
]])

@section('title', $program->name)
@section('page_heading', $program->name)

@section('page_actions')
    <april:button-link href="{{ route('programs.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to programmes
    </april:button-link>
@endsection

@section('content')
    @livewire('program-record', ['program' => $program])
@endsection
