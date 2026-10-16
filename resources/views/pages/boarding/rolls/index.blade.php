@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('boarding-rolls.index'), 'text' => 'Boarding rolls', 'active'],
]])

@section('title', 'Boarding rolls')
@section('page_heading', 'Boarding rolls')

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <livewire:boarding-roll-board />
</div>
@endsection
