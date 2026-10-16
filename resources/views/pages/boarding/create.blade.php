@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.create'), 'text' => 'Open a house', 'active'],
]])

@section('title', __('Open a house'))

@section('page_heading', __('Open a house'))

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <livewire:dormitory-form />
</div>
@endsection
