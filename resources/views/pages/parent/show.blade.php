@extends('adminlte::page')

@section('title', __("$parent->name's profile"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("$parent->name's profile") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'parents'],
        ['href'=> route('parents.show', $parent->id), 'text'=> "View $parent->name's profile", 'active'],
    ]])
@endsection

@section('content')

    @livewire('show-parent-profile', ['parent' => $parent])

    @livewire('display-status')

    
@endsection
