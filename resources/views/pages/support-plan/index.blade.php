@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('support-plans.index'), 'text' => 'Support plans', 'active'],
]])

@section('title', 'Support plans')
@section('page_heading', 'Support plans')

@section('page_actions')
    @can('create', App\Models\SupportPlan::class)
        <april:button-link href="{{ route('support-plans.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Open a plan
        </april:button-link>
    @endcan
@endsection

@section('content')
    @livewire('support-plan-directory')
@endsection
