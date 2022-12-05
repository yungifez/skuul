@extends('adminlte::page')

@section('title', __('Class Groups'))

@section('content_header')
    <h1 class="">
        {{ __('Class Groups') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'Class Groups'],
        ['href'=> route('class-groups.show', $classGroup->id), 'text'=> "View $classGroup->name", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-class-group', ['classGroup' => $classGroup])

    @livewire('display-status')
@endsection
