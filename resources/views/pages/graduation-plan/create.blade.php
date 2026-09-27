@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('graduation-plans.index'), 'text' => 'Graduation plans'],
    ['text' => 'Write a plan', 'active'],
]])

@section('title', 'Write a graduation plan')
@section('page_heading', 'Write a graduation plan')

@section('page_actions')
    <april:button-link href="{{ route('graduation-plans.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to plans
    </april:button-link>
@endsection

@section('content')
    @livewire('create-graduation-plan-form')
@endsection
