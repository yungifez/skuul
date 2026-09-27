@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('facilities.index'), 'text' => 'Facilities', 'active'],
]])

@section('title', __('Facilities'))

@section('page_heading', __('Facilities'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <livewire:facility-board />
</div>
@endsection
