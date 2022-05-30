@extends('adminlte::page')

@section('title', __("view $notice->title"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("View $notice->title") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('notices.index'), 'text'=> 'notices'],
        ['href'=> route('notices.show', $notice->id), 'text'=> "View $notice->title", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-notice', ['notice' => $notice])

    @livewire('display-status')

@endsection
