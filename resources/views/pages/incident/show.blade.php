@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases'],
    ['text' => $incident->reference, 'active'],
]])

@section('title', 'Case '.$incident->reference)
@section('page_heading', $incident->summary)

@section('page_actions')
    <april:button-link href="{{ route('incidents.index') }}" variant="ghost">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Cases
    </april:button-link>
@endsection

@section('content')
    @livewire('show-incident', ['incident' => $incident])
@endsection
