@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('boarding-rolls.index', ['taken_on' => $roll->taken_on->toDateString()]), 'text' => 'Boarding rolls'],
    ['href' => route('boarding-rolls.show', $roll), 'text' => $roll->dormitory->name, 'active'],
]])

@section('title', $roll->type->label())
@section('page_heading', $roll->type->label())

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">{{ $roll->dormitory->name }} · {{ $roll->taken_on->format('l, j F Y') }}</p>

    <livewire:boarding-roll-sheet :roll="$roll" />
</div>
@endsection
