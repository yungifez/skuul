@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('overnight-leaves.index'), 'text' => 'Nights away', 'active'],
]])

@section('title', __('Nights away'))

@section('page_heading', __('Nights away'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <livewire:overnight-leave-desk />
</div>
@endsection
