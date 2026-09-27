@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('support-plans.index'), 'text' => 'Support plans'],
    ['text' => $plan->title, 'active'],
]])

@section('title', $plan->title)
@section('page_heading', $plan->title)

@section('content')
    @livewire('show-support-plan', ['plan' => $plan])
@endsection
