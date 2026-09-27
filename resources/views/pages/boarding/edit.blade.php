@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.show', $dormitory->id), 'text' => $dormitory->name],
    ['text' => 'Change', 'active'],
]])

@section('title', 'Change '.$dormitory->name)

@section('page_heading', 'Change '.$dormitory->name)

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <livewire:dormitory-form :dormitory="$dormitory" />
</div>
@endsection
