@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('admissions.waitlist.index'), 'text' => 'Admissions waitlist', 'active'],
]])

@section('title', __('Admissions waitlist'))
@section('page_heading', __('Admissions waitlist'))

@section('content')
    <livewire:admission-waitlist-board />
@endsection
