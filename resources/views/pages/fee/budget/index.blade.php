@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('budgets.index'), 'text' => 'Budgets', 'active'],
]])

@section('title', __('Budgets'))

@section('page_heading', __('Budgets'))

@section('content')
    @livewire('budget-planner')
@endsection
