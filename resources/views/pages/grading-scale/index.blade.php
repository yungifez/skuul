@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('grading-scales.index'), 'text' => 'Grading scales', 'active'],
]])

@section('title', __('Grading scales'))
@section('page_heading', __('Grading scales'))

@section('content')
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <livewire:grading-scale-manager />
    </div>
@endsection
