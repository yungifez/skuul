@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('users.invitations.index'), 'text' => 'Account invitations', 'active'],
]])

@section('title', __('Account invitations'))

@section('page_heading', __('Account invitations'))

@section('content')
    <livewire:list-account-invitations />
@endsection
