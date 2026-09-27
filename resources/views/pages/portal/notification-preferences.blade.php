@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Notification settings', 'active'],
]])

@section('title', 'Notification settings')
@section('page_heading', 'Notification settings')

@section('content')
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <livewire:notice-email-preferences :is-portal="true" />
    </div>
@endsection
